# CSM-Hub

CSM-Hub is the central website for a growing digital ecosystem focused on learning, technology, creativity, business, and continuous evolution.

## Mission

The project began as a personal website and web development sandbox. Its mission is to turn that foundation into a platform where ideas can be learned, built, created, shared, and evolved into real projects.

## Platform Idea

CSM-Hub is the main corporate and platform experience. It establishes the permanent top-level navigation for three future platform areas:

- **CSM-Hub** - the main homepage and corporate platform.
- **CSM-Store** - the planned e-commerce application.
- **CSM-Blog** - the planned company, ideas, and content application.

The Store and Blog paths currently contain honest placeholders rather than simulated functionality. Their public routes are `store/` and `blog/`; Apache serves each module's `index.html` through its directory URL.

## Current Implementation

This is a dependency-free static website built with:

- Semantic HTML.
- Inline CSS using the existing CSM-Hub color variables.
- Vanilla JavaScript for the accessible mobile navigation.
- Local image assets in `images/`.
- No package manager, database, API, build pipeline, or backend service.

The homepage preserves its internal About, Ecosystem, Projects, and Contact navigation beneath the primary platform navigation. The desktop platform navigation uses a balanced three-column layout, and the mobile menu remains keyboard-accessible with `aria-expanded`, `aria-controls`, and visible focus states.

## Documentation

See [INSTALLATION.md](INSTALLATION.md) for requirements, project structure, local server commands, route details, asset notes, validation steps, and the backup process.

## Backup

The `backup/` directory contains a current snapshot of the project, including documentation, source files, archive content, and image assets. The backup directory is excluded from its own snapshot to prevent recursive copies.

## Collaboration

The platform is intentionally being developed in modules. The next development phases are CSM-Store and CSM-Blog; this repository currently establishes the shared platform identity and navigation foundation.
