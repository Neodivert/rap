#!/bin/bash

# Step 0: Check if we have root privileges and load configuration file.
###############################################################################

if [ "$(whoami)" != "root" ]; then
	printf "ERROR: This command must be run with sudo\n" 1>&2
	exit 1
fi

source config.cfg

# Step 1: Check if user gave us an argument (as expected)
###############################################################################

if [ $# -ne 1 ]; then
	printf "ERROR: \"unistansll_localhost.sh\" expects a path string\n" 1>&2
	printf "\tUsage: ./unistansll_localhost.sh \"path\"\n" 1>&2
	exit 1
fi


# Step 2: Check if DB configuration file is found in path given by user.
# The DB configuration file is neccesary in order to retrieve DB information.
###############################################################################

# Retrieve web path and remove the '/' at the end (if exists).
WEB_PATH=${1%/}

DB_CONFIG_FILE="${WEB_PATH}/php/config/bd.php"
if [ ! -f "$DB_CONFIG_FILE" ]; then
	printf "ERROR: expected file [%s] not found\n" $DB_CONFIG_FILE 1>&2
	exit 1
fi


# Step 3: Retrieve database info.
###############################################################################

printf "Retrieving database info ...\n"

DB_NAME=`grep -o "'bd'.*=>.*" $DB_CONFIG_FILE | sed "s/.*'\(.*\)'.*/\1/g"`
DB_USER_NAME=`grep -o "'usuario'.*=>.*" $DB_CONFIG_FILE | sed "s/.*'\(.*\)'.*/\1/g"`

printf "Retrieving database info ...OK\n"


# Step 4: Wait for user confirmation.
###############################################################################

printf "This uninstall script will perform the following actions: \n"
printf " - Delete MySQL database [%s]\n" $DB_NAME
printf " - Delete MySQL user [%s]\n" $DB_USER_NAME
printf " - DELETE ENTIRE DIRECTORY (INCLUDING USERS CONTENT DIRECTORY) [%s]\n\n" $WEB_PATH

# Ask user for permission.
read -p "Uninstall (DATABASE AND MEDIA DIRECTORY WILL BE DELETED)? (y/n): " -n 1 -r

# Exit if user didn't give us confirmation.
echo # Move to a new line
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
	printf "Uninstall script exited by user\n\n"
	exit 1
fi


# Step 5: Ask the user for him/her administrative MySQL password.
############################################################################### 

# Start XAMPP MySQL
printf "Starting MySQL ...\n"
sudo ${XAMPP_DIRECTORY}/lampp startmysql
printf "Starting MySQL ...OK\n"

# Get mysql command's path
mysql="${XAMPP_DIRECTORY}/bin/mysql"
mysqldump="${XAMPP_DIRECTORY}/bin/mysqldump"

# Ask the user for him/her administrative MySQL password.
MYSQL_PASSWORD=`./utilities/src/get_mysql_user_password.sh "$mysql"`
printf "\n"


# Step 6: Database uninstall
###############################################################################

# Backup RAP content (database and media directory).
MEDIA_DIR="$WEB_PATH/media"
current_date=`date +%H_%M_%S__%d_%m_%Y`
db_backup="rap_backup_$current_date.sql"

printf "Exporting database to file [%s] ...\n" $db_backup
"$mysqldump" -u root --password="${MYSQL_PASSWORD}" "$DB_NAME" > "$db_backup"
printf "Exporting database to file [%s] ...OK\n" $db_backup

printf "Making a backup of database and media dir [%s] ...\n" "${MEDIA_DIR}"
rap_backup="backup_rap_content_$current_date.zip"
zip -r $rap_backup $MEDIA_DIR $db_backup
rm $db_backup
printf "Making a backup of database and media dir [%s] ...OK\n" "${MEDIA_DIR}"

# Delete MySQL user.
printf "Deleting MySQL user [%s] ...\n" "${DB_USER_NAME}"
"$mysql" -u root --password="${MYSQL_PASSWORD}" -e "DROP USER '$DB_USER_NAME'@'localhost';"
printf "Deleting MySQL user [%s] ...OK\n" "${DB_USER_NAME}"

# Delete MySQL database.
printf "Deleting MySQL database [%s] ...\n" "${DB_NAME}"
"$mysql" -u root --password="${MYSQL_PASSWORD}" -e "DROP DATABASE $DB_NAME;"
printf "Deleting MySQL database [%s] ...OK\n" "${DB_NAME}"

# Delete web path.
printf "Deleting web path [%s] ...\n" $WEB_PATH
rm -r $WEB_PATH
printf "Deleting web path [%s] ...OK\n" $WEB_PATH


# Step 7: Done!
###############################################################################

printf "\n\nRAP content backup saved in [%s]\n" $rap_backup
printf "RAP uninstall script has finished\n\n"

exit 0


# Reference
###############################################################################
# 15 Practical Grep Command Examples In Linux / UNIX - The geek stuff
# http://www.thegeekstuff.com/2009/03/15-practical-unix-grep-command-examples/
#
# sed get string between two delimiters - permanent TODO
# http://blog.dragon-tortuga.net/?p=812
#
# How do you append to an already existing string? - Stack Overflow
# http://stackoverflow.com/questions/2250131/how-do-you-append-to-an-already-existing-string
#
# Sintaxis de DROP USER - MySQL 5.0 Reference Manual
# https://dev.mysql.com/doc/refman/5.0/es/drop-user.html
#
# Sintaxis de DROP DATABASE - MySQL 5.0 Reference Manual
# https://dev.mysql.com/doc/refman/5.0/es/drop-database.html
###############################################################################
