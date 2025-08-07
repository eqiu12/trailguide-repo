# Trailguide WordPress Starter (Theme + AI Plugin)

A ready-to-hack WordPress setup for travel guides, designed for **GitHub Codespaces** (or any Docker-capable machine) using **@wordpress/env**.

## Includes
- **themes/trailguide-theme** — FSE block theme with CPT `guide`, taxonomies (`country`, `city`, `theme`), Leaflet map shortcode, JSON-LD.
- **plugins/trailguide-ai** — AI Q&A block and REST endpoint (store your OpenAI API key in WP admin → Settings → Trailguide AI).

## Quick Start (GitHub Codespaces)
1. Create a new **empty** GitHub repo and push this folder.
2. Open the repo in **Codespaces**.
3. The devcontainer will install deps and run `npm run start` automatically.
4. In the terminal, seed mock content:
   ```bash
   npm run seed
   ```
5. When Codespaces forwards **port 8888**, click **Open in Browser** → WordPress will be live.
6. Login: **admin / admin** (created by the seed script).
7. Go to **Settings → Trailguide AI** and paste your **OpenAI API key**.

## Local (no Codespaces)
```bash
npm i
npm run start         # boots WP on http://localhost:8888
npm run seed          # installs WP, activates theme/plugin, creates mock data
npm run stop
npm run destroy       # blow it all away
```

## Notes
- AI Q&A uses `gpt-4o-mini` by default (change in `plugins/trailguide-ai/includes/rest-endpoints.php`).
- Theme templates live under `themes/trailguide-theme/templates`.
- Custom post type: `guide` (slug `/guides`). Blocks/patterns welcome!
- If WP fails to start on Codespaces, run: `npm run stop && npm run destroy && npm run start`.

MIT/GPL where applicable. Enjoy!
