# Custom Fields
Place this app in **nextcloud/apps/**

## Usage
DAV's `PROPPATCH` request type allows adding custom properties to files / folders.
Since these custom properties are not accessible via the frontend, this plugin will list all (DAV) properties of a file or folder.
It is also possible to define "well-known" properties in the admin settings which then will be presented as input fields in the "properties" tab in files app sidebar.
Changing the property in the tab will also change the DAV property and hence it will be possible to access the properties via DAV call.