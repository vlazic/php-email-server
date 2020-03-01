# php-email-server


## Motivation


## Usage


## Installation

Create .env file

```ini
# set 'On' to enable, or 'Off' to disable
DEBUG=Off

# this needs to match $_POST['token'] value sent with request
# you can use some random value from here: https://www.uuidgenerator.net/
TOKEN=

# something like: smtp.gmail.com
SMTP_SERVER=
# usually 26 (no encryption, 465 for SSL, 587 )
SMTP_PORT=587
SMTP_USER=
SMTP_PASSWORD=

# set this correctly to avoid beeing marked as spam 
FROM_EMAIL=

# this will be shown to the receiver, FROM_NAME can be overwritten with $_POST['from_name']
FROM_NAME=
```



