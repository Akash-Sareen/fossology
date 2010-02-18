<?php
/***********************************************************
Copyright (C) 2010 Hewlett-Packard Development Company, L.P.

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
***********************************************************/
/*************************************************
Restrict usage: Every PHP file should have this
at the very beginning.
This prevents hacking attempts.
*************************************************/
global $GlobalReady;
if (!isset($GlobalReady)) {
    exit;
}
class agent_copyright_once extends FO_Plugin {
    public $Name = "agent_copyright_once";
    public $Title = "One-Shot Copyright Analysis";
    // Note: menuList is not needed for this plugin, it inserts into the menu
    // in the code below.
    //public $MenuList = "Upload::One-Shot Bsam";
    public $Version = "1.0";
    public $Dependency = array(
        "view",
        "view-license"
    );
    public $NoHTML = 0;
    /** For anyone to access, without login, use: **/
    // public $DBaccess   = PLUGIN_DB_NONE;
    // public $LoginFlag  = 0;

    /** To require login access, use: **/
    public $DBaccess = PLUGIN_DB_ANALYZE;
    public $LoginFlag = 1;
  /*********************************************
  AnalyzeOne(): Analyze one uploaded file.
  *********************************************/
    function AnalyzeOne($Highlight, $LicCache) {
        global $Plugins;
        global $AGENTDIR;
        $V = "";
        $View = & $Plugins[plugin_find_id("view") ];
        $TempFile = $_FILES['licfile']['tmp_name'];
        $Sys = "$AGENTDIR/statement.py $AGENTDIR/files $TempFile";
        $Fin = popen($Sys, "r");
        //print "<pre>";
        //print $Sys."\n";
        while (!feof($Fin)) {
            $Line = fgets($Fin);
            if (strlen($Line) > 0) {
                //print $Line;
                //print "--------------------------------------------------------------------------------\n";
                $match = array();
                preg_match_all("/\[(?P<start>\d+)\:(?P<end>\d+)\]/", $Line, $match);
                //print_r($match);
                $View->AddHighlight($match['start'][0], $match['end'][0], 0);
            }
        }
        //print "</pre>";
        pclose($Fin);
        if ($Highlight) {
            $Fin = fopen($TempFile, "r");
            if ($Fin) {
                $View->SortHighlightMenu();
                print "<center>";
                print $View->GetHighlightMenu(-1);
                print "</center>";
                print "<hr />\n";
                $View->ShowText($Fin, 0, 1, -1);
                fclose($Fin);
            }
        }
        else {
        }
        /* Clean up */
        return ($V);
    } // AnalyzeOne()
  /*********************************************
  RegisterMenus(): Change the type of output
  based on user-supplied parameters.
  Returns 1 on success.
  *********************************************/
    function RegisterMenus() {
        if ($this->State != PLUGIN_STATE_READY) {
            return (0);
        } // don't run
        $Highlight = GetParm('highlight', PARM_INTEGER);
        if (empty($Hightlight)) {
            $Highlight = 0;
        }
        $ShowHeader = GetParm('showheader', PARM_INTEGER);
        if (empty($ShowHeader)) {
            $ShowHeader = 0;
        }
        if (GetParm("mod", PARM_STRING) == $this->Name) {
            $ThisMod = 1;
        }
        else {
            $ThisMod = 0;
        }
        /* Check for a wget post (wget cannot post to a variable name) */
        if ($ThisMod && empty($_POST['licfile'])) {
            $Fin = fopen("php://input", "r");
            $Ftmp = tempnam(NULL, "fosslic-alo-");
            $Fout = fopen($Ftmp, "w");
            while (!feof($Fin)) {
                $Line = fgets($Fin);
                fwrite($Fout, $Line);
            }
            fclose($Fout);
            if (filesize($Ftmp) > 0) {
                $_FILES['licfile']['tmp_name'] = $Ftmp;
                $_FILES['licfile']['size'] = filesize($Ftmp);
                $_FILES['licfile']['unlink_flag'] = 1;
            }
            else {
                unlink($Ftmp);
            }
            fclose($Fin);
        }
        if ($ThisMod && file_exists(@$_FILES['licfile']['tmp_name']) && ($Highlight != 1) && ($ShowHeader != 1)) {
            $this->NoHTML = 1;
            /* default header is plain text */
        }
        /* Only register with the menu system if the user is logged in. */
        if (!empty($_SESSION['User'])) {
            // Debugging changes to license analysis NOTE: this comment doesn't make sense.
            if (@$_SESSION['UserLevel'] >= PLUGIN_DB_ANALYZE) {
                menu_insert("Main::Upload::One-Shot Copyright", $this->MenuOrder, $this->Name, $this->MenuTarget);
            }
            // Debugging changes to license analysis
            if (@$_SESSION['UserLevel'] >= PLUGIN_DB_DEBUG) {
                $URI = $this->Name . Traceback_parm_keep(array(
                    "format",
                    "item"
                ));
                menu_insert("View::[BREAK]", 100);
                menu_insert("View::bsam One-Shot", 101, $URI, "bsam One-shot, real-time license analysis");
                menu_insert("View-Meta::[BREAK]", 100);
                menu_insert("View-Meta::bsam One-Shot", 101, $URI, "bsam One-shot, real-time license analysis");
            }
        }
    } // RegisterMenus()
  /*********************************************
  Output(): Generate the text for this plugin.
  *********************************************/
    function Output() {
        if ($this->State != PLUGIN_STATE_READY) {
            return;
        }
        global $DB;
        global $DATADIR;
        global $PROJECTSTATEDIR;
        $LicCache = "$PROJECTSTATEDIR/agents/License.bsam";
        $V = "";
        switch ($this->OutputType) {
        case "XML":
            break;
        case "HTML":
            /* If this is a POST, then process the request. */
            $Highlight = GetParm('highlight', PARM_INTEGER); // may be null
            /* You can also specify the file by uploadtree_pk as 'item' */
            $Item = GetParm('item', PARM_INTEGER); // may be null
            if (file_exists(@$_FILES['licfile']['tmp_name'])) {
                if ($_FILES['licfile']['size'] <= 1024 * 1024 * 10) {
                    /* Size is not too big.  */
                    print $this->AnalyzeOne($Highlight, $LicCache) . "\n";
                }
                if (!empty($_FILES['licfile']['unlink_flag'])) {
                    unlink($_FILES['licfile']['tmp_name']);
                }
                return;
            }
            else if (!empty($Item) && !empty($DB)) {
                /* Get the pfile info */
                $Results = $DB->Action("SELECT * FROM pfile
                    INNER JOIN uploadtree ON uploadtree_pk = $Item
                    AND pfile_pk = pfile_fk;");
                if (!empty($Results[0]['pfile_pk'])) {
                    global $LIBEXECDIR;
                    $Highlight = 1; /* processing a pfile? Always highlight. */
                    $Repo = $Results[0]['pfile_sha1'] . "." . $Results[0]['pfile_md5'] . "." . $Results[0]['pfile_size'];
                    $Repo = trim(shell_exec("$LIBEXECDIR/reppath files '$Repo'"));
                    $_FILES['licfile']['tmp_name'] = $Repo;
                    $_FILES['licfile']['size'] = $Results[0]['pfile_size'];
                    if ($_FILES['licfile']['size'] <= 1024 * 1024 * 10) {
                        /* Size is not too big.  */
                        print $this->AnalyzeOne($Highlight, $LicCache) . "\n";
                    }
                    /* Do not unlink the or it will delete the repo file! */
                    if (!empty($_FILES['licfile']['unlink_flag'])) {
                        unlink($_FILES['licfile']['tmp_name']);
                    }
                    return;
                }
            }
            /* Display instructions */
            $V.= "This analyzer allows you to upload a single file for license analysis.\n";
            $V.= "The limitations:\n";
            $V.= "<ul>\n";
            $V.= "<li>The analysis is done in real-time. Large files may take a while. This method is not recommended for files larger than a few hundred kilobytes.\n";
            $V.= "<li>Files that contain files are <b>not</b> unpacked. If you upload a 'zip' or 'deb' file, then the binary file will be scanned for licenses and nothing will likely be found.\n";
            $V.= "<li>Results are <b>not</b> stored. As soon as you get your results, your uploaded file is removed from the system.\n";
            $V.= "</ul>\n";
            /* Display the form */
            $V.= "<form enctype='multipart/form-data' method='post'>\n";
            $V.= "<ol>\n";
            $V.= "<li>Select the file to upload:<br />\n";
            $V.= "<input name='licfile' size='60' type='file' /><br />\n";
            $V.= "<b>NOTE</b>: Files larger than 100K will be discarded and not analyzed.<P />\n";
            $V.= "<li><input type='checkbox' name='highlight' value='1'>Check if you want to see the highlighted licenses.\n";
            $V.= "Unchecked returns a simple list that summarizes the identified license types.";
            $V.= "<P />\n";
            $V.= "</ol>\n";
            $V.= "<input type='hidden' name='showheader' value='1'>";
            $V.= "<input type='submit' value='Analyze!'>\n";
            $V.= "</form>\n";
            break;
        case "Text":
            break;
        default:
            break;
        }
        if (!empty($_FILES['licfile']['unlink_flag'])) {
            unlink($_FILES['licfile']['tmp_name']);
        }
        if (!$this->OutputToStdout) {
            return ($V);
        }
        print ($V);
        return;
    }
};
$NewPlugin = new agent_copyright_once;
?>
