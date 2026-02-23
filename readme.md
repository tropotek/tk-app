# Tk App - Laravel Examples 


## Insstallation With Docker

First copy the `.env.example` file to `.env` and update as required.

```
# Start docker container 
$cd tk-app
$ docker-compose up --build -d

# Open docker terminal in the tk-app container
$ docker exec -it tk-app /bin/bash

# from within the container terminal
$ cd /app
$ ./bin/app-update
```
Once complete then browse to http://localhost:8081

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

