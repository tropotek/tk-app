#!/bin/bash
#
#

# restrict to the app path
SCRIPT=$(realpath "$0")
APP_PATH=$(dirname "$(dirname "$SCRIPT")")
cd "$APP_PATH" || exit 1

FORCE=""
while getopts 'f' opt; do
	case "${opt}" in
		f)
			FORCE=y
			;;
		?)
			echo "usage: ${0} [-f]"
			echo "  -f force fresh db migration and seed"
			exit 1
			;;
	esac
done

# parameters
shift $((OPTIND-1))
#PROFILE="${1:-example}"

# database setup
if [[ "$FORCE" == "y" ]] || [[ ! -f "$APP_PATH/database/storage/database.sqlite" ]]; then

    if [[ "$APP_ENV" == "local" ]]; then
        composer install --no-interaction --prefer-dist
    else
        composer install --no-dev --no-interaction --prefer-dist
    fi

    # generate APP_KEY if none exists
    # test if .env exists, if not skip (for prod)
    if ! grep -q '^APP_KEY=base64:' "$APP_PATH/.env"; then
        echo "Generating APP_KEY..."
        php artisan key:generate --force
        warn "WARNING: New APP_KEY generated."
    fi

    echo "  Creating Database"
    touch "$APP_PATH/database/storage/database.sqlite"

    echo "  Migrating Database"
    php artisan migrate:fresh --seed

    php artisan storage:link

    npm install
fi

npm run build
