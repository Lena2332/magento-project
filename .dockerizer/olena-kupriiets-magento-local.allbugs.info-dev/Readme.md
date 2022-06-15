## Local development - domains ##

Add the following domains to the `/etc/hosts` file:

```shell
127.0.0.1 olena-kupriiets-magento-local.allbugs.info www.olena-kupriiets-magento-local.allbugs.info olena-kupriiets-magento-us.allbugs.info www.olena-kupriiets-magento-us.allbugs.info pma-dev-olena-kupriiets-magento-local.allbugs.info mh-dev-olena-kupriiets-magento-local.allbugs.info
```

Urls list:
- [https://olena-kupriiets-magento-local.allbugs.info](https://olena-kupriiets-magento-local.allbugs.info) 
- [https://www.olena-kupriiets-magento-local.allbugs.info](https://www.olena-kupriiets-magento-local.allbugs.info) 
- [https://olena-kupriiets-magento-us.allbugs.info](https://olena-kupriiets-magento-us.allbugs.info) 
- [https://www.olena-kupriiets-magento-us.allbugs.info](https://www.olena-kupriiets-magento-us.allbugs.info) 
- [http://pma-dev-olena-kupriiets-magento-local.allbugs.info](http://pma-dev-olena-kupriiets-magento-local.allbugs.info) 
- [http://mh-dev-olena-kupriiets-magento-local.allbugs.info](http://mh-dev-olena-kupriiets-magento-local.allbugs.info)


## Local development - self-signed certificates ##

Generate self-signed certificates before running this composition in the `$SSL_CERTIFICATES_DIR`:

```shell
mkcert -cert-file=olena-kupriiets-magento-local.allbugs.info-dev.pem -key-file=olena-kupriiets-magento-local.allbugs.info-dev-key.pem olena-kupriiets-magento-local.allbugs.info www.olena-kupriiets-magento-local.allbugs.info olena-kupriiets-magento-us.allbugs.info www.olena-kupriiets-magento-us.allbugs.info
```

Add these keys to the Traefik configuration file if needed.