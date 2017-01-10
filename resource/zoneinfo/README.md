Instructions for creating zoneinfo files
========================================

### Download vzic
First you need to download vzic library:

https://github.com/libical/vzic or https://sourceforge.net/projects/vzic/

### Download latest tz database files
You can download the data only distribution from:
- https://www.iana.org/time-zones

Unpack to a folder of your choice, you need to specify that folder later.

### GLib2+
To create the time zone files you have to make sure that glib2-dev 
package is installed on the machine where you want to create the files.

### Edit makefile of vzic
There is a README file inside the library that explains the details about 
the changes. The most important settings are:
- **OLSON_DIR**: Absolute path to the latest tz database folder
- **TZID_PREFIX**: this must be an empty value to make sure framework finds the time zones

### Create the files
- Goto the vzic folder
- Run "make"
- Run "vzic --pure" (without pure creation fails at some point)
- Copy the **zoneinfo** folder to the repository
