#!/usr/bin/env python
# -*- encoding: utf-8 -*-

import time
import requests  # http://docs.python-requests.org/en/latest/
import os
import sys
import datetime
import ConfigParser
import codecs

STREAM_CONFIG_FILEPATH = './stream.ini'
STREAM_CONFIG_SECTION = 'tilos-icecast'


def get_config():
    Config = ConfigParser.ConfigParser()
    Config.readfp(codecs.open(STREAM_CONFIG_FILEPATH, 'r', 'UTF-8'))
    config_dict = {}
    options = Config.options(STREAM_CONFIG_SECTION)
    for option in options:
        config_dict[option] = Config.get(STREAM_CONFIG_SECTION, option)
    if config_dict.get('streams') and ':' in config_dict.get('streams'):
        stream_list = config_dict.get('streams').split(':')
        config_dict['streams'] = stream_list
    return config_dict


def get_hour_minute_string(local_time):
    time_obj = datetime.datetime.fromtimestamp(local_time)
    return u'%02d:%02d' % (time_obj.hour, time_obj.minute)


def get_show_title(current_show):
    show_name = current_show.get('show', {}).get('name', '-')
    planned_from = get_hour_minute_string(
        current_show.get('plannedFrom', time.time()))
    planned_to = get_hour_minute_string(
        current_show.get('plannedTo', time.time() + 1 * 60 * 60))
    return u'%s' % (
        show_name,
    )


def find_current_show(json_result):
    if type(json_result) is not list:
        return
    unixtime_now = int(time.time()) + 10  # Add 10 secs to avoid time overlaps
    for element in json_result:
        planned_from = element.get('plannedFrom')
        planned_to = element.get('plannedTo')
        if planned_from is None or planned_to is None:
            continue
        if planned_from <= unixtime_now and planned_to >= unixtime_now:
            return element

# Change to the script directory, where the .ini file most probably is
os.chdir(os.path.dirname(sys.argv[0]))
config_dict = get_config()

api_url = 'http://tilos.hu/api/v0/episode?start=%s&end=%s' % (
    int(time.time() - 4 * 60 * 60),  # Minus four hours
    int(time.time() + 4 * 60 * 60)  # Plus four hours
)
request = requests.get(api_url)
request.close()
if not request.headers['content-type'].startswith('application/json'):
    sys.exit(1)
content = request.json()
current_show = find_current_show(content)
if not current_show:
    sys.exit(1)


show_title = get_show_title(current_show)
show_string = u'%s - %s' % (
    show_title,
    config_dict.get('title-append-text')
)

for mountPoint in config_dict.get('streams'):
    retObj = requests.get(('http://%s:%s/admin/metadata.xsl?'
                          'song=%s&mount=%s&mode=updinfo&charset=UTF-8') % (
        config_dict.get('hostname'),
        config_dict.get('port'),
        show_string,
        mountPoint,
    ),
        auth=(config_dict.get('username'), config_dict.get('password')))
    retObj.close()


# vim: enc=utf-8
