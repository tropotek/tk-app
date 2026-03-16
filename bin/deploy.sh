#!/usr/bin/env bash

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


touch "$APP_PATH/database/storage/database.sqlite"

# generate APP_KEY if none exists (local only)
if [[ "$APP_ENV" == "local" ]] && ! grep -q '^APP_KEY=base64:' "$APP_PATH/.env"; then
    php artisan key:generate --force
fi

# database setup
if [[ "$FORCE" == "y" ]] || [[ ! -f "$APP_PATH/database/storage/database.sqlite" ]]; then
    if [[ "$APP_ENV" == "local" ]]; then
        php artisan migrate:fresh --seed --force
        #php artisan db:seed
    fi
fi

php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan optimize:clear

php artisan optimize

php artisan migrate --force

if [[ "$APP_ENV" == "local" ]]; then
    npm install
    npm run build
else
    rm -f "$APP_PATH/public/hot"
    php artisan config:cache
    php artisan event:cache
fi

# start container
exec "$@"
