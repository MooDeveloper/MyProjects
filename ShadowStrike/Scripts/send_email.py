import smtplib
import sys
import pymysql
from email.mime.text import MIMEText
import random

# Ensure the script gets the correct number of arguments
if len(sys.argv) != 2:
    print("Usage: send_email.py <email>")
    sys.exit(1)

# Input arguments
email = sys.argv[1]

# Database connection details
db_host = "localhost"
db_user = "phpmyadmin"
db_password = "tesla123"
db_name = "shadowstrike"

# Generate a random 6-digit verification code
verification_code = str(random.randint(100000, 999999))

# Save the code to the database
try:
    conn = pymysql.connect(host=db_host, user=db_user, password=db_password, database=db_name)
    cursor = conn.cursor()

    query = "UPDATE users SET verification_code = %s WHERE email = %s"
    cursor.execute(query, (verification_code, email))
    conn.commit()
except Exception as e:
    print(f"Database error: {e}")
    sys.exit(1)
finally:
    cursor.close()
    conn.close()

# Email details
sender_email = "s12011705@stu.najah.edu"
sender_password = "lbci qomv ouyd wluq"

subject = "ShadowStrike - Verification Code"
body = f"Your verification code is: {verification_code}\n\nPlease enter this code on the verification page to verify your email."

msg = MIMEText(body)
msg['Subject'] = subject
msg['From'] = sender_email
msg['To'] = email

# Send the email
try:
    with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
        server.login(sender_email, sender_password)
        server.sendmail(sender_email, email, msg.as_string())
    print("Verification code sent successfully.")
except Exception as e:
    print(f"Error sending email: {e}")
