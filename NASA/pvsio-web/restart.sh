#!/bin/bash
cd ~/pvsio-web
npm install
cd src/server
node pvssocketserver.js restart
