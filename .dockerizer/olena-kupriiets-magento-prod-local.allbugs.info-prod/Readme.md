## Local development - domains ##

Add the following domains to the `/etc/hosts` file:

```shell
127.0.0.1 olena-kupriiets-magento-prod-local.allbugs.info www.olena-kupriiets-magento-prod-local.allbugs.info olena-kupriiets-magento-prod-us.allbugs.info www.olena-kupriiets-magento-prod-us.allbugs.info pma-prod-olena-kupriiets-magento-prod-local.allbugs.info mh-prod-olena-kupriiets-magento-prod-local.allbugs.info
```

Urls list:
- [https://olena-kupriiets-magento-prod-local.allbugs.info](https://olena-kupriiets-magento-prod-local.allbugs.info) 
- [https://www.olena-kupriiets-magento-prod-local.allbugs.info](https://www.olena-kupriiets-magento-prod-local.allbugs.info) 
- [https://olena-kupriiets-magento-prod-us.allbugs.info](https://olena-kupriiets-magento-prod-us.allbugs.info) 
- [https://www.olena-kupriiets-magento-prod-us.allbugs.info](https://www.olena-kupriiets-magento-prod-us.allbugs.info) 
- [http://pma-prod-olena-kupriiets-magento-prod-local.allbugs.info](http://pma-prod-olena-kupriiets-magento-prod-local.allbugs.info) 
- [http://mh-prod-olena-kupriiets-magento-prod-local.allbugs.info](http://mh-prod-olena-kupriiets-magento-prod-local.allbugs.info)


## Local development - self-signed certificates ##

Generate self-signed certificates before running this composition in the `$SSL_CERTIFICATES_DIR`:

```shell
mkcert -cert-file=olena-kupriiets-magento-prod-local.allbugs.info-prod.pem -key-file=olena-kupriiets-magento-prod-local.allbugs.info-prod-key.pem olena-kupriiets-magento-prod-local.allbugs.info www.olena-kupriiets-magento-prod-local.allbugs.info olena-kupriiets-magento-prod-us.allbugs.info www.olena-kupriiets-magento-prod-us.allbugs.info
```

Add these keys to the Traefik configuration file if needed.