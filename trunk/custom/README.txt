PROBID DIRECT DEVELOPMENT DOCUMENTATION

custom directory - This directory houses several sub-directories all with php files that circumvent the Joomla Framework.  Any calls that need to be made on the Joomla server, but do not need (or should not use) the Joomla framework go in here.
SUB-DIRECTORIES
/custom/connections - houses connection php files used by many different php scripts in custom sub-directories
-dbMDB2.php - houses connection string and object to database.  Any file that needs to have access to the db does a require_once with this file.
-dbMDB2-disconnect.php - closes the connection object to the database opened in dbMDB2.php.  Any file that uses dbMDB2.php should include (require_once) this file after to shut down all connections.
-geo-connect.php - houses connection string and object (curl) that makes an http connection to the geo.probiddirect.com server.  Any file that needs to make an http request to geo should include (require_once) this file.
-geo-disconnect.php - disconnects the http object (curl) from geo-connect.php.  Any file that uses geo-connect.php should include (require_once) this file.

/custom/proxy - houses scripts that are accessed via ajax calls from the Joomla website, but need to access data from other servers.  This is not feasible with current browser security restrictions do not allow ajax calls to other servers, so these files act as a proxy.
-geo-update.php - This file is called when any change is made to a listing.  The file is sent a listing_id via an http POST request.  This file determines what type the listing is (user profile OR project) and if the call is an update OR a delete and makes the subsequent call to the Geo server.

-geo-query-projects.php - this file is called to query the Geo server for matching projects.  NEED TO FINISH THIS DOC

-geo-query-providers.php - this file is called to query the Geo server for matching providers.  NEED TO FINISH THIS DOC

/custom/service-providers - houses scripts around connecting Service Providers to Projects.

/custom/tags/ - houses scripts around adding/removing tags to projects

/custom/wall/ - houses scripts that send email notifications for posts to project job cards (wall) and for cleaning up the table that houses the notifications

-cleanUpWallNotifications.php - script should be called by cron on a periodic basis (15 minutes?).  This script simply deletes all rows from the db table for email alerts that have been sent.

-sendWallNotifications.php - script should be called by cron on a periodic basis (1 minute).  This script goes and gets job card (wall) notification posts and puts together the email list of all users on the project minus the user that created the post and then sends everyone on the list an email with a link back to the job-card.  It also updates the db table as email sent for those rows.