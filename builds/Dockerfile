FROM php:7.4-cli-alpine

RUN $(php -r '$extensionInstalled = array_map("strtolower", \get_loaded_extensions(false));$requiredExtensions = ["zlib", "json", "json", "json", "json", "json", "json", "json", "simplexml", "openssl", "fileinfo", "fileinfo", "posix", "posix", "dom", "libxml", "pcre"];$extensionsToInstall = array_diff($requiredExtensions, $extensionInstalled);if ([] !== $extensionsToInstall) {echo \sprintf("docker-php-ext-install %s", implode(" ", $extensionsToInstall));}echo "echo \"No extensions\"";')

COPY /storage /storage
COPY xmlparser /xmlparser

ENTRYPOINT ["/xmlparser"]
