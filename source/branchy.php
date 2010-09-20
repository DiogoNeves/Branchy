<?php

/**
 * To run branchy we have to tell which target are we loading. We do so by setting btarget on the
 * http request.
 * Branch doesn't know how each project deals with errors and warnings so, the check for 'btarget'
 * must be done before requiring Branchy.
 * It will assert if not found.
 *
 * A valid btarget is:
 *	- a string
 *	- 64 char long, maximum
 * e.g. '?btarget=contactform'
 *
 * How does it work?
 *		There are two tables in the dataBase: main_branch, content;
 *
 *		'main_branch' has information and options regarding the main branch (e.g. Path);
 *		Currently we only support 128 char long paths
 *
 *		'content' contains a simple look-up table that maps btarget with a real file and in case no
 *		entry is found branchy can be set to search for a file with the same name (+ '.php');
 */

assert( '!empty( $_GET[ \'btarget\' ] )' );

//
// DON'T FORGET! Branch paths in the DataBase must always be relative to the defined base path
//

// Base path were to find all branches
define( 'BRANCH_BASE_PATH',		'branches/' );

// Get safe target
$target = $_GET[ 'btarget' ];
$target = mysql_escape_string( $target );

// Confirm this is a valid btarget
// It must be done after escaping because the string changes
assert( 'is_string( $target )' );
$length = strlen( $target );
assert( '$length > 0 && $length <= 64' );

// Connect to database to get information
$dataBase = new mysqli( 'localhost', 'root', '', 'branchy' );
if ( $dataBase->connect_errno )
{
	die( "Unable to connect to DataBase: {$dataBase->connect_error}" );
}

// Main loader code
$path = GetMainBranchPath( $dataBase );

assert( 'is_string( $path )' ); // Just in case we messed up the database table
assert( 'strlen( $path ) > 0' );
assert( 'strlen( $path ) <= 128' );

$path = NormalisePath( $path ); // So that we can use $path immediately :)

// Now it's the target path turn :)
$targetPath = GetTargetPath( $dataBase, $target );

assert( 'is_string( $path )' ); // Just in case we messed up the database table
assert( 'strlen( $path ) <= 128' );

if ( empty( $targetPath ) )
{
	// Target not found in the database, use default behaviour
	$targetPath = "$target.php";
}

$targetPath = NormalisePath( $targetPath );

// Create full file path
$targetPath = BRANCH_BASE_PATH . "$path$targetPath";

// Check if target file exists
if ( file_exists( $targetPath ) )
{
	// Ok, we're done, include the file for execution
	include $targetPath;
}
else
{
	echo "Couldn't find the requested file: '$targetPath'<br/>";
}

// Say goodbye
$dataBase->close();
unset( $dataBase );


//
// Methods
//

/**
 * Get the main branch path from the Database
 *
 * @param mysqli $dataBase Database where to get the path from
 * @return string Path to the main branch (relative to the app path)
 */
function GetMainBranchPath( mysqli $dataBase )
{
	assert( 'is_object( $dataBase )' );
	
	$branchQuery = "SELECT path FROM main_branch WHERE branch_uid=0;";
	$result = $dataBase->query( $branchQuery );

	// Look for errors and log them
	if ( !$result )
	{
		LogAndDieSafely( $dataBase, "MySQL error<br/><br/>$branchQuery<br/>{$dataBase->errno}: {$dataBase->error}" );
	}

	// Check if we found the path
	if ( $result->num_rows != 1 )
	{
		LogAndDieSafely( $dataBase, "Couldn't find the main branch path" );
	}

	// Ready! Fetch and return path
	return $result->fetch_object()->path;
}

/**
 * Looks-up for a target path in the content table on the database
 *
 * @param mysqli $dataBase Database where to look for the target
 * @param string $target Target to look-up
 * @return string The target path or "" if not in the database
 */
function GetTargetPath( mysqli $dataBase, $target )
{
	assert( 'is_object( $dataBase )' );
	assert( 'is_string( $target )' );

	$target = mysql_escape_string( $target );
	
	$targetQuery = "SELECT path FROM content WHERE target_name='$target';";
	$result = $dataBase->query( $targetQuery );

	// Look for errors and log them
	if ( !$result )
	{
		LogAndDieSafely( $dataBase, "MySQL error<br/><br/>$targetQuery<br/>{$dataBase->errno}: {$dataBase->error}" );
	}

	assert( '$result->num_rows <= 1' );

	// Ready! Fetch and return path
	return ($result->num_rows == 1) ? $result->fetch_object()->path : "";
}

/**
 * Log an error and die safely destroying the database object.
 *
 * @param mysqli $dataBase (Reference) Database to destroy
 * @param string $message Message to log
 * @param bool $verbose If true, prints the message to the client
 */
function LogAndDieSafely( mysqli &$dataBase, $message, $verbose = false )
{
	assert( 'is_object( $dataBase )' );
	assert( 'is_string( $message )' );
	assert( 'is_bool( $verbose )' );
	
	// Safely close database
	$dataBase->close();
	unset( $dataBase );
	
	// Log message and die
	error_log( $message );
	die( $message );
}

/**
 * Normalises a path by removing / from the end of the path and adding / to the start
 *
 * @param string $path Path to normalise
 * @return string Normalised path
 */
function NormalisePath( $path )
{
	assert( 'is_string( $path )' );

	// Remove stupid spaces
	$path = trim( $path );
	
	// Remove '/' from the start and end
	$path = ltrim( $path, '/' );
	$path = rtrim( $path, '/' ); // This simplifies the next line
	
	// Add '/' to the end
	$path = "/$path";
	
	return $path;
}

?>