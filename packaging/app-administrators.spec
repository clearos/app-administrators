
Name: app-administrators
Epoch: 1
Version: 2.0.0
Release: 1%{dist}
Summary: Administrators
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base
Requires: app-base >= 1:1.4.40
Requires: app-accounts
Requires: app-groups
Requires: app-policy-manager

%description
With the Administrators app, you can grant access to specific apps to groups of users on the system.

%package core
Summary: Administrators - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-accounts-core
Requires: app-policy-manager-core

%description core
With the Administrators app, you can grant access to specific apps to groups of users on the system.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/administrators
cp -r * %{buildroot}/usr/clearos/apps/administrators/


%post
logger -p local6.notice -t installer 'app-administrators - installing'

%post core
logger -p local6.notice -t installer 'app-administrators-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/administrators/deploy/install ] && /usr/clearos/apps/administrators/deploy/install
fi

[ -x /usr/clearos/apps/administrators/deploy/upgrade ] && /usr/clearos/apps/administrators/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-administrators - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-administrators-core - uninstalling'
    [ -x /usr/clearos/apps/administrators/deploy/uninstall ] && /usr/clearos/apps/administrators/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/administrators/controllers
/usr/clearos/apps/administrators/htdocs
/usr/clearos/apps/administrators/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/administrators/packaging
%dir /usr/clearos/apps/administrators
/usr/clearos/apps/administrators/deploy
/usr/clearos/apps/administrators/language
/usr/clearos/apps/administrators/libraries
