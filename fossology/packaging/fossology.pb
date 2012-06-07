#
# Project Builder configuration file
# For project fossology
#
# "$Id: $"
#

#
# What is the project URL
#
#pburl fossology = svn://svn.fossology.org/fossology/devel
#pburl fossology = svn://svn+ssh.fossology.org/fossology/devel
#pburl fossology = cvs://cvs.fossology.org/fossology/devel
pburl fossology = svn+https://fossology.svn.sourceforge.net/svnroot/fossology/tags/2.0.0
#pburl fossology = ftp://ftp.fossology.org/src/fossology-devel.tar.gz
#pburl fossology = file:///src/fossology-devel.tar.gz
#pburl fossology = dir:///src/fossology-devel

# Repository
pbrepo fossology = http://fossology.org
#pbml fossology = fossology-announce@lists.fossology.org
#pbsmtp fossology = localhost

# Check whether project is well formed 
# when downloading from ftp/http/...
# (containing already a directory with the project-version name)
#pbwf fossology = 1

#
# Packager label
#
pbpackager fossology = Dong Ma <vincent@fossology.org>
#

# For delivery to a machine by SSH (potentially the FTP server)
# Needs hostname, account and directory
#
sshhost fossology = localhost
sshlogin fossology = delivery
sshdir fossology = /var/ftp/pub/fossology
sshport fossology = 22

#
# Global version/tag for the project
#
projver fossology = 2.0.0
projtag fossology = 1
# Hash of valid version names
version fossology = trunk

# Is it a test version or a production version
testver fossology = false

# Additional repository to add at build time
# addrepo centos-5-x86_64 = http://packages.sw.be/rpmforge-release/rpmforge-release-0.3.6-1.el5.rf.x86_64.rpm,ftp://ftp.project-builder.org/test/centos/5/pb.repo
# addrepo centos-4-x86_64 = http://packages.sw.be/rpmforge-release/rpmforge-release-0.3.6-1.el4.rf.x86_64.rpm,ftp://ftp.project-builder.org/test/centos/4/pb.repo

# Adapt to your needs:
# Optional if you need to overwrite the global values above
#
#pkgver fossology = stable
#pkgtag fossology = 3
# Hash of default package/package directory
defpkgdir fossology =  
# Hash of additional package/package directory
#extpkgdir minor-pkg = dir-minor-pkg

# List of files per pkg on which to apply filters
# Files are mentioned relatively to pbroot/defpkgdir
#filteredfiles fossology = Makefile.PL,configure.in,install.sh,fossology.8
#supfiles fossology = fossology.init

# For perl modules, names are different depending on distro
# Here perl-xxx for RPMs, libxxx-perl for debs, ...
# So the package name is indeed virtual
#namingtype fossology = perl

addrepo rhel-5-i386 = http://packages.sw.be/rpmforge-release/rpmforge-release-0.3.6-1.el5.rf.i386.rpm,ftp://ftp.project-builder.org/test/centos/5/pb.repo
addrepo rhel-5-x86_64 = http://packages.sw.be/rpmforge-release/rpmforge-release-0.3.6-1.el5.rf.x86_64.rpm,ftp://ftp.project-builder.org/test/centos/5/pb.repo
addrepo centos-4-i386 = http://packages.sw.be/rpmforge-release/rpmforge-release-0.3.6-1.el4.rf.i386.rpm,ftp://ftp.project-builder.org/test/centos/4/pb.repo
addrepo centos-4-x86_64 = http://packages.sw.be/rpmforge-release/rpmforge-release-0.3.6-1.el4.rf.x86_64.rpm,ftp://ftp.project-builder.org/test/centos/4/pb.repo