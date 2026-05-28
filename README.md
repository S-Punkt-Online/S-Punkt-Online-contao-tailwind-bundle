# Contao Tailwind Bundle

Tailwind CSS integration for Contao 5.

## Features

- Tailwind CSS v4 support
- Console build command
- Watch command for development
- Automatic frontend CSS integration
- Contao backend module
- Build status display
- Cron command
- Database class scanner for Contao content
- Support for custom theme colors via CSS

## Installation

```bash
composer require s-punkt-online/contao-tailwind-bundle
npm install tailwindcss @tailwindcss/cli --save-dev
```

## Build CSS
```
php vendor/bin/contao-console tailwind:build
```

## Watch mode
```
php vendor/bin/contao-console tailwind:watch
```
## Cron
```
php vendor/bin/contao-console tailwind:cron
```

Example cronjob:
```
*/15 * * * * cd /path/to/contao && php vendor/bin/contao-console tailwind:cron
```

## Custom colors

Create: config/tailwind-theme.css

Example:
```
@theme {
--color-primary-50: #eff6ff;
--color-primary-100: #dbeafe;
--color-primary-500: #3b82f6;
--color-primary-700: #1d4ed8;
}
```

Then use the classes in Contao:
```
<div class="bg-primary-500 text-white p-6 rounded-xl">
  Content
</div>
```

After changes, rebuild Tailwind CSS in the backend module or via command.

## Backend module

The extension adds a backend module:

System → Tailwind CSS

There you can:

* check build status
* see the last build time
* see the last cron execution
* rebuild Tailwind CSS manually

## License
MIT