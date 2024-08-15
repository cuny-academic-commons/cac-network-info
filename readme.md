# CAC Network Info

When running a WordPress Network, it can be difficult to track which plugins and themes are being used by which sites. Existing solutions do not scale well, because they loop through every site on the network to collect information. So we at the [CUNY Academic Commons](https://commons.gc.cuny.edu/) developed this tool to provide a more efficient way to collect and query this information.

The plugin has no UI. It is intended to be used only at the command line.

## Installation

1. Install the plugin on your network. It can be installed either as a standard plugin (in which case it must be activated networkwide), or you can bootstrap it via mu-plugins.
2. Create the database tables: `wp cac-network-info database install`
3. The plugin automatically collects information about active plugins and themes on activation/deactivation events. However, you'll need to run a one-time command at installation to fill the database with existing data: `wp cac-network-info sync --all-sites`.

## Usage

### Syncing individual sites

Information about active plugins and themes on individual sites is tracked automatically. But if things do get out of sync, you can sync one or more individual sites using `wp cac-network-info sync`:

```bash
wp cac-network-info sync 1234 # accepts numeric site IDs
wp cac-network-info sync mysite.example.com # accepts site URLs
wp cac-network-info sync 1234 1235 # accepts multiple site identifiers
```

As noted in the installation instructions, you can sync all sites at once using `wp cac-network-info sync --all-sites`.

### Querying the database

You can query the database directly to get information about plugin and theme usage. Or you can use our handy `query` command to get information about a specific plugin or theme::

```bash
wp cac-network-info query plugin jetpack
wp cac-network-info query theme twentytwenty
```

This data can be formatted for export to CSV:

```bash
wp cac-network-info query plugin jetpack --format=csv --porcelain > ~/sites-using-jetpack.csv
```

It can also be convenient to get just a list of URLs or IDs, for when you need to script further actions:

```bash
wp cac-network-info query plugin jetpack --format=csv --porcelain --fields=url
```
