#!/usr/bin/env bash
#
# Use this as the app entrypoint to bootstrap the code
# in a docker container for startup
#


# restrict to the app path
SCRIPT=$(realpath "$0")
APP_PATH=$(dirname "$(dirname "$SCRIPT")")
cd "$APP_PATH" || exit 1

FRESH=""
while getopts 'f' opt; do
	case "${opt}" in
		f)
			FRESH=y
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


if [[ "$APP_ENV" == "local" ]]; then
    composer install --no-interaction --prefer-dist
else
    composer install --no-dev --no-interaction --prefer-dist
fi

# Create required Laravel directories for storage and caching
mkdir -p "$APP_PATH/storage/framework/{sessions,views,cache}" \
   && mkdir -p "$APP_PATH/storage/logs" \
   && mkdir -p "$APP_PATH/bootstrap/cache"

touch "$APP_PATH/database/storage/database.sqlite"

# generate APP_KEY if none exists (local only)
if [[ "$APP_ENV" == "local" ]] && ! grep -q '^APP_KEY=base64:' "$APP_PATH/.env"; then
    php artisan key:generate --force
fi

# database setup
if [[ "$FRESH" == "y" ]] || [[ ! -f "$APP_PATH/database/storage/database.sqlite" ]]; then
    if [[ "$APP_ENV" == "local" ]]; then
        php artisan migrate:fresh --seed --force
    fi
else
    php artisan migrate --force
fi

php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan optimize:clear

if [[ "$APP_ENV" == "local" ]]; then
    npm install
    npm run build
else
    rm -f "$APP_PATH/public/hot"
    php artisan config:cache
    php artisan event:cache
fi

php artisan optimize

# start container
exec "$@"
