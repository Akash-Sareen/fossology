/*********************************************************************
Copyright (C) 2011 Hewlett-Packard Development Company, L.P.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*********************************************************************/

#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <CUnit/CUnit.h>
#include <CUnit/Automated.h>
#include <gio/gio.h>
#include <glib.h>

#include <libfossology.h>
#include <testRun.h>

#include <agent.h>
#include <database.h>
#include <event.h>
#include <host.h>
#include <interface.h>
#include <logging.h>
#include <scheduler.h>

/* ************************************************************************** */
/* **** suite initializations *********************************************** */
/* ************************************************************************** */

/**
 * We don't want to actually generate any error messages. To do this, the log
 * file will be set to /dev/null.
 *
 * @return -1 on failure, 0 of success
 */
int init_suite(void)
{
  if(log_file && fclose(log_file) != 0)
    return -1;
  log_file = stdout;
  return 0;
}

/**
 * Since we changed the log file in the initializations, we need to close it
 * and set the pointer to NULL so that the logging system can reset it to the
 * correct value.
 *
 * @return -1 of failure, 0 on success
 */
int clean_suite(void)
{
  job_list_clean();
  host_list_clean();
  agent_list_clean();
  interface_destroy();
  database_destroy();
  event_loop_destroy();

  log_file = NULL;
  return 0;
}

/* ************************************************************************** */
/* *** main and suite decl ************************************************** */
/* ************************************************************************** */

/* create test suite */
CU_SuiteInfo suites[] =
{
    {"agent.c: meta",                init_suite,            clean_suite, tests_meta_agent       },
    //{"agent.c:",               agent_init_suite,      agent_clean_suite, tests_agent            },
    {"host.c:",                      init_suite,            clean_suite, tests_host             },
    {"event.c:",                     init_suite,            clean_suite, tests_event            },
    {"interface.c:",       interface_init_suite,  interface_clean_suite, tests_interface        },
    {"interface_thread:", interface_thread_init, interface_thread_clean, tests_interface_thread },
    CU_SUITE_INFO_NULL
};

int main( int argc, char *argv[] )
{
  g_type_init();
  g_thread_init(NULL);

  focunit_main(argc, argv, "scheduler_Tests", suites) ;

  return 0;
}