FROM postgres:18-trixie

RUN set -eux; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
	locales; \
	apt-get clean; \
	rm -rf /var/lib/apt/lists/*; \
	sed -i '/fr_FR.UTF-8/s/^# //' /etc/locale.gen; \
	locale-gen;

ENV TZ=Europe/Paris
ENV LANG=fr_FR.UTF-8
ENV LANGUAGE=fr_FR:fr
ENV LC_ALL=fr_FR.UTF-8

RUN set -eux; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
	# ca-certificates: for accessing remote raster files;
	# fix: https://github.com/postgis/docker-postgis/issues/307
		ca-certificates \
		postgresql-postgis=3.5.2+dfsg-1 \
		postgresql-18-pgtap; \
	rm -rf /var/lib/apt/lists/*

ADD https://gitlab.com/dalibo/postgresql_anonymizer/-/package_files/248481847/download /tmp/postgresql_anonymizer_pg18-2.5.1.amd64.deb

RUN set -eux; \
	apt-get install \
	/tmp/*.deb \
	-y --no-install-recommends; \
	rm /tmp/*.deb;
