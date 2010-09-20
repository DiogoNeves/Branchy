Branchy is a light-weight framework to manage and load php files from different branches.

## Whish list

* Simple branch load (all files need to be available in the branch folder)
* Simple branch viewer
* Simple branch editor (add and remove functionality)
* Database Wrapper (so that the user can specify it's own DB connection)
* Rename/Move branch editor functionality
* Allow a file to be in a different branch then the main branch (and editor support)
* Add user specific branches (and editor support)
* A/B testing support

## Setup

Use the .sql file under 'setup' folder to setup the necessary tables (and test content) to run Branchy.

NOTE: Currently we only support 128 char long paths and 64 char long target names;

## How does it work?

Branchy uses the database to load all information.

There are two tables in the dataBase:
# main_branch - has information and options regarding the main branch (e.g. Path);
# content - contains a simple look-up table that maps btarget with a real file and in case no entry is found branchy can be set to search for a file with the same name (+ '.php');