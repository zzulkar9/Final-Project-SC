FROM php:8.1.10

# Set the working directory
WORKDIR /var/www/html

# Copy your PHP application files
COPY . /var/www/html

# Expose the port if needed
# EXPOSE 80

# Start the PHP application
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
