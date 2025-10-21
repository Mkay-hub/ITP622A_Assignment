# Start from Microsoft’s PHP 8.2 container (safe and easy)
FROM mcr.microsoft.com/vscode/devcontainers/php:0-8.2

# Install a few basic tools (optional but helpful)
RUN apt-get update && apt-get install -y git unzip && rm -rf /var/lib/apt/lists/*

# Set the working directory (where your project files live)
WORKDIR /workspace
