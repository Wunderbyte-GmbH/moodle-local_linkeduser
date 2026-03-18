# Update linked login (local_linkeduser) #

This Moodle local plugin manages the synchronisation between local Moodle user
accounts and their OAuth2 Identity Provider (IdP) linked login records. It
prevents identity-theft scenarios that can arise when an OAuth2 provider allows
multiple users to share the same email address.

## Features ##

### Keep local email vs. use IdP email ###

By default the plugin keeps the email address stored in the Moodle `user` table
and updates the `auth_oauth2_linked_login` record to match it. Enabling the
**"Use Identity Provider email"** admin setting reverses this: the IdP email is
written back to the Moodle user record instead.

### Identity Provider username prefix ###

Some Identity Providers prefix local usernames with a string before returning
them. For example:

| Where        | Username                |
|--------------|-------------------------|
| Local Moodle | `rssmra98d08h501h`      |
| IdP           | `tinit_rssmra98d08h501h` |

The **"Identity Provider username prefix"** setting lets you specify the prefix
(e.g. `tinit_`). When set, the plugin constructs the expected IdP username by
lowercasing `<prefix><local_username>` and stores it in the linked login record.
This ensures Moodle can correctly match the incoming IdP credential to the right
local account.

Leave the setting empty when usernames are identical between Moodle and the IdP.

## Admin settings ##

Navigate to **Site administration → Plugins → Local plugins → Create OAuth2
linked users** to configure the plugin.

| Setting | Default | Description |
|---------|---------|-------------|
| Use Identity Provider email | Off | When enabled the IdP email overwrites the local Moodle email. |
| Identity Provider username prefix | *(empty)* | Prefix prepended by the IdP to local usernames (e.g. `tinit_`). |

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/linkeduser

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2023 Georg Maißer <info@wunderbyte.at>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
