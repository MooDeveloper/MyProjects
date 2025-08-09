import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
import sys

def send_welcome_email(user_email):
    # Gmail credentials
    sender_email = "s12011705@stu.najah.edu"
    sender_password = "lbci qomv ouyd wluq"  # You can use app-specific passwords if 2FA is enabled

    # Email setup
    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = user_email
    msg['Subject'] = "Welcome to ShadowStrike Tool"

    # HTML email body
    html_content = f"""
    <html>
        <head>
        <title>Welcome to ShadowStrike</title>
        </head>
        <body>
        <h2>Hello, {user_email}!</h2>
        <p>Welcome to ShadowStrike tool! Your account has been successfully verified. You can now use the platform to access all the features.</p>
        <p>We are excited to have you on board!</p>
        </body>
    </html>
    """

    # Attach HTML content to the message
    msg.attach(MIMEText(html_content, 'html'))

    # Connect to Gmail's SMTP server
    try:
        with smtplib.SMTP('smtp.gmail.com', 587) as server:
            server.starttls()  # Secure the connection
            server.login(sender_email, sender_password)  # Log in to Gmail's SMTP server
            server.sendmail(sender_email, user_email, msg.as_string())  # Send email
            print(f"Welcome email sent to {user_email}.")
    except Exception as e:
        print(f"Error: {e}")

# Get email from command line argument
if len(sys.argv) > 1:
    user_email = sys.argv[1]
    send_welcome_email(user_email)
else:
    print("No email provided.")
