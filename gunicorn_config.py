bind = '127.0.0.1:8000'
backlog = 2048

workers = 8
worker_class = 'sync'
worker_connections = 1000
timeout = 30
keepalive = 2

umask = 0
errorlog = '-'
loglevel = 'info'
accesslog = '-'
access_log_format = '%(h)s %(l)s %(u)s %(t)s "%(r)s" %(s)s %(b)s "%(f)s" "%(a)s"'