
import sys

def extract():
    if (len(sys.argv) != 2):
        print("Usage: python3 crawl-device.py <username>")
        raise ValueError("Exactly one argument is required.")
    username = sys.argv[1]
    print(f"Username: {username}")
    return username

print("HI")
username = extract()
print(f"Here's the user from printhi: {username}")