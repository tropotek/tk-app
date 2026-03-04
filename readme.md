# Tk App - Laravel Examples 


## Installation With Docker



```
copy the .env example file and update as required
$ cd /tk-app        # project path
$ cp .env.example .env
$ cd app
$ cp .env.example .env

# Start dev docker container
$ cd /tk-app
$ docker compose up --build -d

# Open docker terminal in the tk-app container
$ docker exec -it tk-app /bin/bash

# from within the container terminal
$ cd /tk-app
$ ./app-update
```
Once complete, then browse to http://localhost:8081

You can login to the site using U: admin@example.com P: password

Shutdown the container:
```
$ docker compose down
```

Tail the container log:
```
$ docker logs -f tk-app
```

Tail the laravel log:
```
$ cd /app
$ tail -f storage/logs/laravel.log
```


## Issues

If you experience permission issues, run the following in your PC terminal:
```
$ cd tk-app
$ sudo chown -R $USER:$USER .
```


# Tk-Base Package

Thi package contains all libs for base functionality like Menus, Breadcrumbs, Tables, etc...

View the [tk-base readme](app/packages/ttek/tk-base/src/readme.md) here.
