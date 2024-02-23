
import sys

def extract():
    if (len(sys.argv) != 2):
        print("Usage: python3 crawl-device.py <username>")
        raise ValueError("Exactly one argument is required.")
    username = sys.argv[1]
    return username

print("\nHI")
username = extract()
print(f"User from printhi: {username}")