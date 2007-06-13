[Setup]
AppName=USVN
AppVerName=Userfriendly SVN
OutputBaseFilename=USVN_add-on_172
AppPublisher=Userfriendly SVN
AppPublisherURL=http://www.usvn.info
AppSupportURL=http://www.usvn.info
AppUpdatesURL=http://www.usvn.info
DefaultDirName=c:\wamp
;DisableDirPage=yes
DefaultGroupName=WampServer
LicenseFile=.\Files\Licence_CeCILL_V2-en.txt
SourceDir=.\

WizardImageFile=.\Files\logo.bmp
;SetupIconFile=.\Files\USVN.ico
;InfoBeforeFile=D:\wampserver\install_files\php\license.txt
;InfoAfterFile=D:\wampserver\install_files\mysql\readme.txt
AlwaysRestart=yes

[Tasks]
; NOTE: The following entry contains English phrases ("Create a desktop icon" and "Additional icons"). You are free to translate them into another language if required.
;Name: "autostart"; Description: "Automatically launch WAMP5 on startup. If you check this option, Services will be installed as automatic. Otherwise, services will be installed as manual and will start and stop with the service manager."; GroupDescription: "Auto Start:" ;Flags: unchecked;

[Files]
Source: ".\Files\svn-1.4.3-setup.exe"; DestDir: "{app}\USVN\"; Flags:  ignoreversion recursesubdirs deleteafterinstall;AfterInstall: InstallSVN('{app}\USVN\svn-1.4.3-setup.exe')
Source: ".\Files\mod_authz_svn.so"; DestDir: "{app}\Apache2\modules\"; Flags:  ignoreversion recursesubdirs;
Source: ".\Files\mod_dav_svn.so"; DestDir: "{app}\Apache2\modules\"; Flags:  ignoreversion recursesubdirs; AfterInstall: ConfigApache('{app}')
Source: ".\Files\intl3_svn.dll"; DestDir: "{app}\Apache2\bin\"; Flags:  ignoreversion recursesubdirs;
Source: ".\Files\libdb44.dll"; DestDir: "{app}\Apache2\bin\"; Flags:  ignoreversion recursesubdirs;
Source: ".\Files\usvn.conf"; DestDir: "{app}\Apache2\conf\alias\"; Flags:  ignoreversion recursesubdirs; AfterInstall: ConfigAlias('{app}')
Source: "..\www\*.*"; DestDir: "{app}\USVN\"; Flags:  ignoreversion recursesubdirs ; AfterInstall: ConfigAlias('{app}')
[Code]
var
  ResultCode: Integer;
  batfile: String;
  
procedure InstallSVN(FileName: String);
begin
  if Exec(ExpandConstant(FileName), '', '', SW_SHOW,
     ewWaitUntilTerminated, ResultCode) then
  begin
    // handle success if necessary; ResultCode contains the exit code
  end
  else begin
    // handle failure if necessary; ResultCode contains the error code
  end;

end;

procedure ConfigApache(FileName: String);

begin

  FileName:= ExpandConstant(FileName);

  FileCopy(FileName + '\Apache2\bin\libapr-1.dll', FileName + '\Apache2\bin\libapr.dll', false);
  FileCopy(FileName + '\Apache2\bin\libaprutil-1.dll', FileName + '\Apache2\bin\libaprutil.dll', false);
  FileCopy(FileName + '\Apache2\bin\libapriconv-1.dll', FileName + '\Apache2\bin\libapriconv.dll', false);

end;

procedure ConfigAlias(FileName: String);
var SrcContent4: String;
var FileName2: String;

begin
  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  StringChange (FileName2, '\','/');
  LoadStringFromFile (FileName + '\Apache2\conf\alias\usvn.conf', SrcContent4);
  StringChangeEx(SrcContent4, 'Alias /usvn/ "c:/usvn/"', 'Alias /usvn/' + ' "' + FileName2 + '/USVN/"', True);
  StringChangeEx(SrcContent4, '<Directory "c:/usvn/">', '<Directory "' + FileName2 + '/USVN/">', True);
  DeleteFile (FileName + '\Apache2\conf\alias\usvn.conf');
  SaveStringToFile(FileName + '\Apache2\conf\alias\usvn.conf',SrcContent4, false);


  LoadStringFromFile (FileName + '\Apache2\conf\httpd.conf', SrcContent4);
  if Pos('Include "' + FileName2 + '/apache2/conf/alias/usvn.conf"', SrcContent4) = 0 then
  begin
    SaveStringToFile(FileName + '\Apache2\conf\httpd.conf', #13#10 + 'Include "' + FileName2 + '/apache2/conf/alias/usvn.conf"' + #13#10, true);
  end;
  
end;

[Run]
;Filename: "{app}\USVN\svn-1.4.3-setup.exe"; Description: "Launch SVN installation now"; Flags:shellexec postinstall skipifsilent runhidden
