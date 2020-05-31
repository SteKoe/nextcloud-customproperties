# Custom Properties
Place this app in **nextcloud/apps/customfields**

## Usage
DAV's `PROPPATCH` request type allows adding custom properties to files / folders.
Since these custom properties are not accessible via the frontend, this plugin will list all (DAV) properties of a file or folder.
It is also possible to define "well-known" properties in the admin settings which then will be presented as input fields in the "properties" tab in files app sidebar.
Changing the property in the tab will also change the DAV property and hence it will be possible to access the properties via DAV call.

## API
It is also possible to mange custom properties via Rest API (admin privileges required):

| Verb          | Endpoint                                              | Purpose                  |
| ------------- | ----------------------------------------------------- | ------------------------ |
| GET           | /index.php/apps/customproperties/customproperties     | Get defined properties   |
| POST          | /index.php/apps/customproperties/customproperties     | Create new property      |
| DELETE        | /index.php/apps/customproperties/customproperties/:id | Delete existing property |

### Example
```bash
curl -X POST \
     -u admin:admin \
     --header "Content-Type: application/json" \
     -d '{"propertylabel": "testasd"}' 
     http://localhost:8080/index.php/apps/customproperties/customproperties
```

## Screenshots
![Tab view in sidebar](.readme/sidebartab.png)
![Settings in admin panel](.readme/adminsettings.png)
