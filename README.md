# Scripts to integrate Snipe-IT with other systems

These are all completely unofficial and very hack-y scripts to modify Snipe-IT.  **Use at your own risk.**

The way they are written isn't really appropriate for merging into Snipe-IT, but if someone wants to take these ideas and make something that can be added to the official version, please do!

## Script Information

### Dymo Label Printing

Dymo has a nice javascript based API for using their label makers.  This takes advantage of that to use a Dymo Labelwriter 450 or similar Dymo label printer to print asset tag stickers.
A link is added to each asset page to take you to the label printing page. Requires the computer have the Dymo label web service installed.

To customize your label, create a label in the Dymo software, with a QR code object named QRCODE, a text box named ASSETNUMBER, and another text box named DATE. The objects will be replaced with a QR code linking to the asset page, the asset number, and the purchase date.

Replace the `var labelXML=` XML content in `web/labels/printlabel.js` with the contents of your `.label` file. (I'm aware that this is a terrible way to update the label, but it works for me for now...if someone want to make this pull from a file instead, that would be nice).

### Dell Warranty Lookup

Puts a link on the asset page that redirects you to Dell Canada's warranty page based on the `Dell Service Tag` custom field for an asset.

### Sync AD Users

The `syncadusers.php` script runs as a cron job and syncs various LDAP fields from AD to Snipe-IT.  It calls the regular ldap sync to create all the users first.

This lets you add company, department, location, title and phone number to the Snipe-IT users.

## Installation

### Configuration
Copy `config.sample.php` to `config.php` and enter your information.  The LDAP settings are only required if using the AD sync script.

### Web server setup
Some of the scripts run from the webserver. To loosely integrate them with Snipe-IT, you must add this to your apache configuration:

```
        Alias /custom "/opt/snipe-it-scripts/web"
        <Directory /opt/snipe-it-scripts/web>
                Require all granted
                AllowOverride All
                Options +Indexes
        </Directory>
```

That will allow all the scripts to be accessed at the `/custom` URL.

### Custom links in asset page

Requires modifying `/resources/views/hardware/view.blade.php`. Add this line just after the `@endif` and just before the `</tbody>` around line 468:
```
@include ('partials.customassetlinks')
```

Link to the file with `ln -s /opt/snipe-it-scripts/customassetlinks.blade.php /opt/snipe-it/resources/views/partials/`

You may need to edit parts of `customassetlinks.blade.php` to match the database field names for custom fields.

Make sure all caches get cleared by running this in the Snipe-IT root directory:
```
php artisan clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

and clear browser cache.


