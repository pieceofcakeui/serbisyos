import os
import sys

# Get the path to this directory (where app.py and this file are)
# and insert it at the beginning of the system path
sys.path.insert(0, os.path.dirname(__file__))

# Import the 'app' instance from your application file (app.py)
# and assign it to the 'application' variable expected by the WSGI server.
# Ito ang nagpapagana sa inyong Flask application.
from app import application 

# The 'application' variable is now your Flask app instance.
