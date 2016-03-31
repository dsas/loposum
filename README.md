# loposum
Creates dummy translations of pot files

This is useful to help visually verify that every string in an app is marked up for translation correctly.

The dummy translations are created by rot-13ing the message text.

## Installation

```bash
git clone https://github.com/dsas/loposum
cd loposum
composer install
```

## Usage
/path/to/repo/bin/loposum translate input-file output-file

If the input file has been partially translated (is a PO not a POT)then those translations are kept.

## Licence

MIT
