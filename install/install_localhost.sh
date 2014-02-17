#!/bin/bash

# Step 0: Check if we have root privileges.
###############################################################################

if [ "$(whoami)" != "root" ]; then
	printf "ERROR: This command must be run with sudo\n" 1>&2
	exit 1
fi


# Step 1: Load configuration file.
###############################################################################

# Load configuration file.
source config.cfg

# If the given path contains a "/" at the end, remove it.
XAMPP_DIRECTORY=${XAMPP_DIRECTORY%/}

# Construct the web path.
WEB_PATH="${XAMPP_DIRECTORY}/htdocs/${WEB_NAME}"


# Step 2: Wait for user confirmation.
###############################################################################

# Tell the user the steps that this instalation script will perform.
printf "The instalation script will perform the following steps: \n";
printf "* Copying web data to [%s]\n" "${WEB_PATH}"
printf "* Creating mysql database and user [%s]\n" "${DB_NAME}"
printf "If you want to change any configuration, exit this install and edit the file \"config.cfg\" before trying again.\n\n"

# Ask user for permission.
read -p "Install? (y/n): " -n 1 -r

# Exit if user didn't give us confirmation.
echo # Move to a new line
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
	printf "Install script exited by user\n\n"
	exit 1
fi


# Step 3: Check if XAMPP is installed where the user said.
###############################################################################

utilities/src/check_if_exist.sh "${XAMPP_DIRECTORY}/lampp"
if [ $? -ne 0 ] ; then
	exit 1
fi

exit 0


# Step 4: Check if given web path isn't already in use.
###############################################################################

# Check if web path isn't already in use.
if [ -d "${WEB_PATH}" ]; then
	printf "ERROR: web path [%s] already in use\n" "${WEB_PATH}" 1>&2
	exit 1
fi


# Step 5: Start XAMPP's MySQL server.
###############################################################################

printf "Starting MySQL ...\n"
sudo ${XAMPP_DIRECTORY}/lampp startmysql
printf "Starting MySQL ...OK\n"

# Get mysql command's path
mysql="${XAMPP_DIRECTORY}/bin/mysql"


# Step 6: Ask the user for him/her administrative MySQL password.
###############################################################################
# Ask the user for him/her administrative MySQL password.
MYSQL_PASSWORD=`./install_utilities/src/get_mysql_user_password.sh "$mysql"`
printf "\n"


# Step 7: Check if a MYSQL database and/or user with the given names already 
# exist.
###############################################################################

# Check if a MySQL database with the given name already exists.
if [[ ! -z "`$mysql -u root --password="${MYSQL_PASSWORD}" -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$DB_NAME'" 2>&1`" ]];
then
	printf "ERROR: database [%s] already exists\n" $DB_NAME 1>&2
	exit 1
fi

# Check if a MySQL user with the given name already exists.
if [[ ! -z "`$mysql -u root --password=${MYSQL_PASSWORD} -e "SELECT 1 FROM mysql.user WHERE user = '$DB_USER_NAME'" 2>&1`" ]] ;then
	printf "ERROR: MySQL user [%s] already exists\n" $DB_USER_NAME 1>&2
	exit 1
fi


# Step 8: Database instalation
###############################################################################

# Ask the user for a password for the database user.
read -e -s -p "Write a password for the GCS database user: " DB_USER_PASSWORD
echo

# Create the database
printf "Creating database [%s] ...\n" "${DB_NAME}"
"$mysql" -u root --password="${MYSQL_PASSWORD}" -e "create database ${DB_NAME}"
printf "Creating database [%s] ...OK\n" "${DB_NAME}"

# Import the database structure from file ../bd/bd-gcs.sql.
printf "Importing database structure from file ...\n"
"$mysql" -u root --password="${MYSQL_PASSWORD}" "${DB_NAME}" < ../bd/bd_gcs.sql
printf "Importing database structure from file ...OK\n"

# Create the database user.
printf "Creating mysql user [%s] ...\n" "${DB_USER_NAME}"
"$mysql" -u root --password="${MYSQL_PASSWORD}" -e "CREATE USER '${DB_USER_NAME}'@'localhost' IDENTIFIED BY '${DB_USER_PASSWORD}';"
printf "Creating mysql user [%s] ...OK\n" "${DB_USER_NAME}"

# Allow the created user to perfom SELECT on the database.
DB_USER_PRIVILEGES="DELETE, INSERT, SELECT, UPDATE"

printf "Giving [%s] privileges to user [%s] ...\n" "${DB_USER_PRIVILEGES}" "${DB_USER_NAME}"
$mysql -u root --password="${MYSQL_PASSWORD}" -e "GRANT ${DB_USER_PRIVILEGES} ON ${DB_NAME}.* TO '${DB_USER_NAME}'@'localhost';"
"$mysql" -u root --password="${MYSQL_PASSWORD}" -e "FLUSH PRIVILEGES;"
printf "Giving [%s] privileges to user [%s] ...OK\n" "${DB_USER_PRIVILEGES}" "${DB_USER_NAME}"


# Step 9: Directory instalation
###############################################################################

printf "Copying web content to [%s] ...\n" "${WEB_PATH}"
sudo cp -r "../web" "${WEB_PATH}"
sudo chown -R "$APACHE_USER" "${WEB_PATH}"
sudo chmod -R 0755 "${WEB_PATH}"
printf "Copying web content to [%s] ...OK\n" "${WEB_PATH}"

utilities_file="${WEB_PATH}/php/utilities.php"
printf "utilities_file: [%s]\n" "$utilities_file"

printf "Personalizing web configuration ...\n"
sudo sed -i "s/~~DB_USER_NAME~~/'${DB_USER_NAME}'/g" "$utilities_file"
sudo sed -i "s/~~DB_USER_PASSWORD~~/'${DB_USER_PASSWORD}'/g" "$utilities_file"
sudo sed -i "s/~~DB_NAME~~/'${DB_NAME}'/g" "$utilities_file"
printf "Personalizing web configuration ...OK\n"


# Step 10: Done!
###############################################################################

printf "\n\nInstall finished. Now you can visit \"localhost/$WEB_NAME\"\n\n"
exit 0
