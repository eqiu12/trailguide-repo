#!/usr/bin/env bash
set -euo pipefail

# Helper to run WP-CLI inside wp-env container
WPCMD="npx wp-env run cli --"

echo "Waiting for WordPress to be ready..."
sleep 5

# Basic install if not already
$WPCMD wp core install --url="http://localhost:8888" --title="Trailguide Dev" --admin_user="admin" --admin_password="admin" --admin_email="admin@example.com" || true

# Activate theme and plugin
$WPCMD wp theme activate trailguide-theme || true
$WPCMD wp plugin activate trailguide-ai || true

# Pretty permalinks
$WPCMD wp rewrite structure '/%postname%/' --hard
$WPCMD wp rewrite flush --hard

# Create tax terms
$WPCMD wp term create country "Japan" --slug=japan || true
$WPCMD wp term create country "Italy" --slug=italy || true
$WPCMD wp term create country "France" --slug=france || true
$WPCMD wp term create city "Tokyo" --slug=tokyo || true
$WPCMD wp term create city "Kyoto" --slug=kyoto || true
$WPCMD wp term create city "Paris" --slug=paris || true
$WPCMD wp term create theme "Food" --slug=food || true
$WPCMD wp term create theme "History" --slug=history || true
$WPCMD wp term create theme "Family" --slug=family || true

# Generate 12 sample guides
for i in {1..12}; do
  TITLE=$(printf "Sample Guide %02d" "$i")
  ID=$($WPCMD wp post create --post_type=guide --post_status=publish --post_title="$TITLE" --post_content="
  <h2>Overview</h2>
  <p>This is mock content for $TITLE with a handy AI Q&A block available in the sidebar.</p>
  <h2>Highlights</h2>
  <ul><li>Museum visit</li><li>Local food street</li><li>Sunset viewpoint</li></ul>
  [tg_map lat='48.8566' lng='2.3522' zoom='12' height='260px']
  " --porcelain)
  # Set excerpt
  $WPCMD wp post update $ID --post_excerpt="A short description for $TITLE."
  # Assign random terms
  $WPCMD wp set-term $ID country $(shuf -e japan italy france -n 1)
  $WPCMD wp set-term $ID city $(shuf -e tokyo kyoto paris -n 1)
  $WPCMD wp set-term $ID theme $(shuf -e food history family -n 1)
done

echo "Done. Login with admin / admin. Open the 'WordPress' forwarded port in Codespaces."
