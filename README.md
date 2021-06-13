# DNA2BPM

DNA²BPM is an Open Source BPM Suite based on BPMN2.0 standard, it has all the main components needed to design and run BPMN2.0 diagrams plus some other tools.

  - Integrated User Experience
  - BPM Designer (based on Oryx)
  - Execution Engine
  - Simulation Engine (test before release)
  - Case Manager
  - Diagram Browser
  - Integrated Inbox (send/recive from/to BPM processes)
  - Key Process Indicator editor (KPI)
  - Integrated RBAC (Role Based Access Control)
  - Multiple connectors (file, Mongo, MySQL,QR-output, etc)

DNA2BPM is a full blown, ready to use,BPMS build on open source software made to evolve.

### Prequisites
In order to test and run dna2bpm you will need:

Git (optional)
Apache or Nginx with rewrite engine on for CodeIgniter.

MongoDB
php5
php5-mongo MongoDB database driver

### RUN local
clone repository (or just copy docker-compose file)
``` 
docker-compose up -d
``` 
then go to http://localhost/setup to start the setup wizard

### Build Image
for an stand -alone image
```
docker build -t $DOCKER_IMAGENAME:$VERSION .
```
### Version
Beta 0.9.10

### Change Log
https://gitlab.com/dna2/origin/commits/development