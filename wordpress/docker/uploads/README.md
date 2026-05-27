# Put your licensed zips here

Place these two files in this folder (`wordpress/docker/uploads/`) BEFORE running `docker compose up`:

| File | Where to get it |
|---|---|
| `Divi.zip` | https://www.elegantthemes.com/members-area/ → Downloads → Divi |
| `acf-pro.zip` | https://www.advancedcustomfields.com/my-account/ → Downloads |

Rename them to **exactly** `Divi.zip` and `acf-pro.zip`.

If you don't have ACF Pro, the installer falls back to the free ACF plugin, but you'll lose the Repeater + Gallery fields the seeder uses (rooms will still create, but room galleries and the Site Content options page will be partial).

This folder is mounted read-only into the installer container.
