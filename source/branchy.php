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
define( 'BRANCH_DEFAULT_PATH',	'default/' ); // Relative to BRANCH_BASE_PATH

// Get safe target
$target = $_GET[ 'btarget' ];
$target = mysql_escape_string( $target );

// Confirm this is a valid btarget
// It must be done after escaping because the string changes
assert( 'is_string( $target )' );
$length = strlen( $target );
assert( $length > 0 && $length <= 64 );

// Connect to database to get information
$dataBase = new mysqli( 'localhost', 'root', '', 'branchy' );
if ( $dataBase->connect_errno )
{
	die( "Unable to connect to DataBase: {$dataBase->connect_error}" );
}

// Main loader code
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

// Ready! Read path
$path = $result->fetch_object()->path;

assert( 'is_string( $path )' ); // Just in case we messed up the database table
assert( 'strlen( $path ) > 0' );
assert( 'strlen( $path ) <= 128' );

NormalisePath( $path ); // So that we can use $path immediately :)

// TODO: Get target file path from Database (if not found check if we can use default)

// Create full file path
// TODO: Use the next line in case it doesn't have a 'content' entry
$targetPath = BRANCH_BASE_PATH . "$path$target.php";

// Check if target file exists
if ( file_exists( $targetPath ) )
{
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

function LogAndDieSafely( mysqli &$dataBase, $message, $verbose = false )
{
	// Safely close database
	$dataBase->close();
	unset( $dataBase );
	
	// Log message and die
	error_log( $message );
	die( $message );
}

function NormalisePath( $path )
{
	if ( strrpos( $path, '/' ) !== (strlen( $path ) - 1) )
	{
		$path .= '/';
	}
	return $path;
}

?>