Branchy is a light-weight framework to manage and load php files from different branches.

## What can or will I be able to do with Branchy?

* Organise the project in live servers
* Test the live server without user making changes immediately public
* Move all or some users to new versions without stopping the service at any point
* Do simple A/B tests
* Update the live server using a patch and only move users when it's ready and fully tested
* Quickly revert server updates
* Edit, push, revert branches easily with a simple web interface

## Finished Features

* Simple branch load

## On the way

* Simple branch viewer

## Wish list

* Simple branch editor (add and remove functionality)
* Rename/Move branch (editor functionality)
* Attach different users to different branches (and editor support)
* A/B testing support
* Database Wrapper (allow for abstract database accesses, currently only supports MySQL)

## Setup

Use the .sql file under 'setup' folder to setup the necessary tables (and test content) to run Branchy.

NOTE: Currently we only support 128 char long paths and 64 char long target names;

## How does it work?

Branchy uses the database to load all information.

There are two tables in the dataBase:
main_branch - has information and options regarding the main branch (e.g. Path);
content - contains a simple look-up table that maps btarget with a real file and in case no entry is found branchy can be set to search for a file with the same name (+ '.php');

## Test

After setting the database, try changing the 'main_branch' value to 'test2', then back to 'test1';
You can also send a request for '?btarget=lookup'