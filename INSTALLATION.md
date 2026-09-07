# CSM-Hub Installation Guide

## 1. Project Overview

CSM-Hub is the main corporate and platform website for the CSM digital ecosystem. The current implementation is a dependency-free static website. It uses HTML, inline CSS, and inline vanilla JavaScript; there is no build step, package manager, database, API, or application server required.

The current mission is to provide a central platform for learning, building, creating, evolving, and eventually connecting the CSM-Hub, CSM-Store, and CSM-Blog experiences.

## 2. Requirements

Required:

- A modern web browser with JavaScript enabled.
- A local copy of this repository.

Recommended for local development:

- Python 3 for a simple static HTTP server, or
- Visual Studio Code with a static-file server extension such as Live Server.

Not required:

- Node.js or npm.
- A database.
- Environment variables.
- A backend service.
- A compilation or bundling tool.

## 3. Project Structure

```text
CSM-Hub/
|-- index.html                 Main CSM-Hub homepage.
|-- .htaccess                  Apache routing and 404 fallback configuration.
|-- apache/
|   `-- csm-hub.conf           Apache virtual directory configuration snippet.
|-- httpd-erro-padrao.html    Maintenance and Apache httpd review page.
|-- INSTALLATION.md            This installation and technical guide.
|-- README.md                  Project overview and change summary.
|-- LICENSE                    Repository license.
|-- images/
|   |-- Main-Logo-CSM-Exp.JPG
|   `-- Main-Logo-CSM-Exp - Favicon.JPG
|-- archive/
|   `-- index-BKP.html         Archived legacy homepage.
|-- store/
|   `-- index.html             CSM-Store module placeholder.
|-- blog/
|   `-- index.html             CSM-Blog module placeholder.
`-- backup/                    Current project snapshot and backup instructions.
```

The `backup/` directory is intentionally excluded from its own snapshot to prevent recursive backups.

## 4. Installation from GitHub

1. Clone or download the repository.
2. Open the `CSM-Hub` directory in Visual Studio Code or another editor.
3. Confirm that `index.html`, `images/`, `store/`, and `blog/` exist.
4. Start a local static server using one of the methods below.
5. Open the homepage at the server URL.

The project should be served from the `CSM-Hub` directory itself. Do not place it behind a URL prefix unless the image paths in `index.html` are updated accordingly.

Apache must allow overrides for this directory so that `.htaccess` can apply. The relevant virtual host or directory configuration should allow `AllowOverride FileInfo Indexes` (or `AllowOverride All`). On Fedora installations using `AllowOverride None`, install the provided `apache/csm-hub.conf` snippet instead.

## 5. Run with Python

From inside the `CSM-Hub` directory:

```bash
python3 -m http.server 8000
```

Open:

```text
http://localhost:8000/index.html
```

Stop the server with `Ctrl+C`.

If port `8000` is busy, choose another port:

```bash
python3 -m http.server 8080
```

Then open `http://localhost:8080/index.html`.

## 6. Run with Visual Studio Code

1. Open the `CSM-Hub` directory.
2. Install or enable a static-file server extension.
3. Open `index.html`.
4. Use the extension command to serve or preview the file.
5. Verify that the browser URL serves the whole `CSM-Hub` directory.

A static server is preferred over opening the file directly because it more accurately reproduces deployed relative-link behavior.

## 7.1 HTTPD Maintenance Page

`httpd-erro-padrao.html` is a standalone fallback page for situations where the Apache httpd server responds but the expected CSM-Hub application is unavailable or the configuration requires review. It preserves the CSM-Hub platform navigation and uses relative links, so it can be placed inside the CSM-Hub document root without hard-coded hostnames.

The page specifically directs administrators to review the virtual host, document root, `DirectoryIndex`, and `/etc/httpd/conf.d/welcome.conf`. It is not a replacement for Apache error handling or a substitute for fixing the underlying configuration.

The root `.htaccess` configures `ErrorDocument 404 /csm-hub/httpd-erro-padrao.html`. Therefore, missing files and routes below `/csm-hub/`, including missing Store or Blog routes, use the CSM-Hub maintenance page. The `/csm-hub/` URL prefix must match the Apache `Alias`, virtual host, or document-root mapping; update the `ErrorDocument` path if the platform is deployed under another prefix.

For the Fedora Apache setup described above:

```bash
sudo cp apache/csm-hub.conf /etc/httpd/conf.d/csm-hub.conf
sudo apachectl configtest
sudo systemctl reload httpd
curl -i http://localhost/csm-hub/route-that-does-not-exist
```

The final command should return HTTP `404` and the CSM-Hub maintenance page body. The status remains `404`, which is important for crawlers and clients even though the branded fallback page is displayed.

## 7. Direct File Opening

The homepage can be opened directly as a local file in a browser. This is useful for a quick content check, but it is not the preferred development method. Some browser security rules and the current favicon/image URL can behave differently when using a `file://` URL.

## 8. Application Routes

The platform navigation currently uses these relative destinations:

- `./index.html` - always returns to the CSM-Hub homepage.
- `store/` - current CSM-Store module route; Apache serves its `index.html`.
- `blog/` - current CSM-Blog module route; Apache serves its `index.html`.

The Store and Blog pages are architectural placeholders only. They do not provide e-commerce or publishing functionality yet. Their next implementation phase should replace the placeholder while preserving these paths.

## 9. Homepage Architecture

`index.html` contains the complete homepage:

- Document metadata and title.
- Inline CSS variables and responsive styles.
- Sticky semantic header and navigation.
- Primary platform navigation for CSM-Hub, CSM-Store, and CSM-Blog.
- Secondary homepage navigation for About, Ecosystem, Projects, and Contact.
- Hero section.
- About section and principles.
- Ecosystem cards.
- Projects placeholder.
- Contact section.
- Footer.
- Inline mobile navigation JavaScript.

The page uses semantic elements including `header`, `nav`, `ul`, `li`, `a`, `main`, `section`, `article`, and `footer`.

## 10. Styling System

The CSS is inline in `index.html`. The primary variables are:

- `--color-primary: #0f172a`
- `--color-secondary: #2563eb`
- `--color-accent: #06b6d4`
- `--color-white: #ffffff`
- `--color-light: #f8fafc`
- `--color-border: #e2e8f0`
- `--color-text: #0f172a`
- `--color-text-secondary: #475569`
- `--color-dark: #020617`
- `--container-width: 1200px`

The main desktop platform navigation uses a three-column grid: the Hub link is left-aligned, Store is centered, and Blog is right-aligned. At widths below `700px`, the navigation becomes a vertical menu controlled by the mobile button. At widths below `900px`, selected content grids collapse to one column.

## 11. JavaScript Behavior

The homepage uses a small inline vanilla JavaScript controller:

1. It locates `#mobileMenuButton` and `#mainNavigation`.
2. Clicking the button toggles the `active` class on the navigation.
3. It updates `aria-expanded` to `true` or `false`.
4. It uses `aria-controls="mainNavigation"` to identify the controlled region.
5. A data attribute prevents duplicate initialization if legacy source content is encountered.

There are no JavaScript dependencies or build artifacts.

## 12. Assets and Paths

Images are stored in `images/`. The homepage references the CSM Experience logo and favicon. The current homepage contains legacy `//localhost/csm-hub/...` image URLs; a deployment that does not expose the site at `/csm-hub/` should convert those references to relative paths such as `images/Main-Logo-CSM-Exp.JPG`.

Do not hard-code a development hostname into new links. Platform navigation uses relative paths so it works on local servers, GitHub-based hosting, and other static hosts.

## 13. Validation Checklist

After installation, verify:

- `index.html` opens without a browser parse error.
- The header remains visible while scrolling.
- CSM-Hub is left, CSM-Store is centered, and CSM-Blog is right on desktop.
- The mobile button opens and closes the navigation.
- `aria-expanded` changes when the mobile menu is toggled.
- Keyboard focus is visible on links and the menu button.
- The internal About, Ecosystem, Projects, and Contact links still work.
- The Store and Blog links resolve to their placeholder pages.
- Images load when the site is served from the expected path.
- There is no horizontal overflow at desktop, tablet, or mobile widths.

## 14. Backup Process

A current project snapshot is stored in `backup/`. It contains the project files as they exist at the time of this documentation update, excluding the `backup/` directory itself.

For a future manual backup:

1. Stop any process that is writing project files.
2. Create a new dated backup directory inside `backup/`, for example `backup/2026-09-05/`.
3. Copy the project root contents into that directory.
4. Exclude `backup/` from the copy so backups do not contain backups.
5. Confirm that HTML, Markdown, license, archive, and image files are present.
6. Record the backup date and source revision in a small manifest file.

Example from the parent directory:

```bash
mkdir -p backup/2026-09-05
find . -mindepth 1 -maxdepth 1 ! -name backup -exec cp -a {} backup/2026-09-05/ \;
```

For a production project, use version control as the primary history and treat this folder as a convenient local snapshot, not as a replacement for Git or off-site backups.

## 15. Next Development Modules

The next planned modules are:

1. CSM-Store at `store/` or a future Store application rooted at `store/`.
2. CSM-Blog at `blog/` or a future Blog application rooted at `blog/`.

Neither module should be considered complete until its placeholder is replaced with its own documented application and its platform navigation marks the current platform as active.
