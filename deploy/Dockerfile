# Attribution: Helen Hou-Sandi https://github.com/10up/action-wordpress-plugin-deploy/
FROM debian:stable-slim

RUN apt-get update \
	&& apt-get install -y subversion rsync git \
	&& apt-get clean -y \
	&& rm -rf /var/lib/apt/lists/* \
	&& git config --global user.email "rd@wpengine.com" \
	&& git config --global user.name "rd@wpengine.com"

COPY entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
WORKDIR /workspace
