# Development Readme

## Maintenance

Download of raw xml files:

- https://download.geofabrik.de/europe/germany/nordrhein-westfalen/koeln-regbez-latest.osm.bz2
- https://download.geofabrik.de/europe/germany-latest.osm.bz2

### Rebuilding and distributing osm files (OpenStreetMap)

Rebuilding the index files takes days to complete. Also including of the
index files into the repository is not suitable - they are way too big
for git.

Therefor the index files are downloaded during build time and only included
into the docker images. So the docker image will always have the current
index files included.
