# Architecture

## Enrichment

The enrichment process begins with a basic HTTP request to the target website using curl. A flexible and extendable set
of enrichment modules is available, all following a standardized enrichment interface. This design allows easy expansion
with additional modules.

Currently, the existing enrichment modules include:

- GeoLocationEnricher – Determines the hosting server’s geographic location.
- IPEnricher – Extracts and adds the website’s IP address.
- MXEnricher – Uses DNS data to gather mailing (MX) information.
- SSLEnricher – Extracts details from the website’s SSL certificate.

All enriched data is incorporated into the classifiers as part of the HTTP response.

## Classification

(TBD – No details provided.)

## Aggregation

During report generation, an aggregation process compiles a snapshot of the current data, rather than using live
information. The export mechanism is defined via an interface, allowing data to be stored in JSON format for later
website use or optionally exported as an HTML file.
