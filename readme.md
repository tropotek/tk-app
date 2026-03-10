# Tk App - Laravel Docker Example 

# Introduction 

This is an experimental app working with Docker and Laravel.

# Contents

- [Installation](#docker-installation)
- [Common Issues](#common-issues)
- [Templating Starndards](app/resources/views/readme.md)
- [tk-base Package](app/packages/ttek/tk-base/src/readme.md)
    - [Breadcrumbs](app/packages/ttek/tk-base/src/Breadcrumbs/readme.md)
    - [Form Templates](app/packages/ttek/tk-base/resources/views/components/form/readme.md)
    - [Menu Builder](app/packages/ttek/tk-base/src/Menu/readme.md)
    - [Table Builder](app/packages/ttek/tk-base/src/Table/readme.md)


## Docker Installation

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
$ tail -f app/storage/logs/laravel.log
```


## Common Issues

If you experience permission issues, run the following in your PC terminal:
```
$ cd tk-app
$ sudo chown -R $USER:$USER .
```

