import sys

class Session:
    def __init__(self):
        self.data = {}

    def set(self, key, value):
        self.data[key] = value

    def get(self, key):
        return self.data.get(key, None)

# Creating a session
SESSION = Session()

# Storing username in the session variable
if len(sys.argv) != 2:
    print("Usage: python3 crawl-device.py <username>")
    exit()

username = sys.argv[1]
SESSION.set("username", username)

# Testing the ability to grab the data and print it
username = SESSION.get("username")
if username is not None:
    print("Username:", username)
else:
    print("Username not found in session.")

