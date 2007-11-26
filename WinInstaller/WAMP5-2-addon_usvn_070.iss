[Setup]
AppName=USVN
AppVerName=Userfriendly SVN
OutputBaseFilename=USVN_07_wamp_add-on
AppPublisher=Userfriendly SVN
AppPublisherURL=http://www.usvn.info
AppSupportURL=http://www.usvn.info
AppUpdatesURL=http://www.usvn.info
DefaultDirName=c:\wamp
DefaultGroupName=USVN

LicenseFile=.\Files\Licence_CeCILL_V2-en.txt
SourceDir=.\

WizardImageFile=.\Files\logo.bmp
AlwaysRestart=yes

[Tasks]
; NOTE: The following entry contains English phrases ("Create a desktop icon" and "Additional icons"). You are free to translate them into another language if required.
;Name: "autostart"; Description: "Automatically launch WAMP5 on startup. If you check this option, Services will be installed as automatic. Otherwise, services will be installed as manual and will start and stop with the service manager."; GroupDescription: "Auto Start:" ;Flags: unchecked;

[Files]
Source: ".\Files\svn-1.4.5-setup.exe"; DestDir: "{app}\USVN\"; Flags:  ignoreversion recursesubdirs deleteafterinstall;AfterInstall: InstallSVN('{app}\USVN\svn-1.4.5-setup.exe')
Source: ".\Files\mod_authz_svn.so"; DestDir: "{app}\bin\apache\apache2.2.6\modules\"; Flags:  ignoreversion recursesubdirs;
Source: ".\Files\mod_dav_svn.so"; DestDir: "{app}\bin\apache\apache2.2.6\modules\"; Flags:  ignoreversion recursesubdirs; AfterInstall: ConfigApache('{app}')
Source: ".\Files\libdb44.dll"; DestDir: "{app}\bin\apache\apache2.2.6\bin\"; Flags:  ignoreversion recursesubdirs;
Source: ".\Files\intl3_svn.dll"; DestDir: "{app}\bin\apache\apache2.2.6\bin\"; Flags:  ignoreversion recursesubdirs;

Source: ".\Files\usvn.conf"; DestDir: "{app}\alias\"; Flags:  ignoreversion recursesubdirs; AfterInstall: ConfigAlias('{app}')
Source: ".\Files\USVN\*.*"; DestDir: "{app}\USVN\"; Flags:  ignoreversion recursesubdirs ; AfterInstall: ConfigAlias('{app}')
Source: ".\Files\config.ini"; DestDir: "{app}\USVN\"; Flags:  ignoreversion recursesubdirs; AfterInstall: ConfigConfig('{app}')
Source: ".\Files\install.bat"; DestDir: "{app}\USVN\"; Flags:  ignoreversion recursesubdirs; AfterInstall: ConfigInstallBAT('{app}')
Source: ".\Files\info.txt"; DestDir: "{app}\USVN\"; Flags: isreadme ignoreversion recursesubdirs ;AfterInstall: InfoBox('{app}')
Source: ".\Files\version.ini"; DestDir: "{app}\USVN\"; Flags:  ignoreversion recursesubdirs;

[Icons]
Name: "{group}\USVN"; Filename: "{app}\USVN\info.txt";
Name: "{group}\USVN website"; IconFilename: ".\Files\usvn.ico"; Filename: "http://www.usvn.info";

[Code]
var
  ResultCode: Integer;
  URL: TInputQueryWizardPage;
  URLstring: String;

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

  FileCopy(FileName + '\bin\apache\apache2.2.6\bin\libapr-1.dll', FileName + '\bin\apache\apache2.2.6\bin\libapr.dll', false);
  FileCopy(FileName + '\bin\apache\apache2.2.6\bin\libaprutil-1.dll', FileName + '\bin\apache\apache2.2.6\bin\libaprutil.dll', false);
  FileCopy(FileName + '\bin\apache\apache2.2.6\bin\libapriconv-1.dll', FileName + '\bin\apache\apache2.2.6\bin\libapriconv.dll', false);

end;

procedure ConfigAlias(FileName: String);
  var SrcContent4: String;
  var FileName2: String;
  var URI: String;
  var i: Integer;
begin
  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  URI:= URL.Values[0];
  i := Length(URI);
  Delete(URI, i, 1);
  StringChange (FileName2, '\','/');
  LoadStringFromFile (FileName + '\alias\usvn.conf', SrcContent4);
  StringChangeEx(SrcContent4, 'Alias /usvn "c:/usvn/"', 'Alias '+ URI + ' "' + FileName2 + '/USVN/"', True);
  StringChangeEx(SrcContent4, '<Directory "c:/usvn/">', '<Directory "' + FileName2 + '/USVN/">', True);
  
  StringChangeEx(SrcContent4, '	SVNParentPath c:/usvn/', '	SVNParentPath "' + FileName2 + '/USVN/files/svn"', True);
  StringChangeEx(SrcContent4, '	AuthUserFile c:/usvn/', '	AuthUserFile "' + FileName2 + '/USVN/files/htpasswd"', True);
  StringChangeEx(SrcContent4, '	AuthzSVNAccessFile c:/usvn/', '	AuthzSVNAccessFile "' + FileName2 + '/USVN/files/authz"', True);
  
  DeleteFile (FileName + '\alias\usvn.conf');
  SaveStringToFile(FileName + '\alias\usvn.conf',SrcContent4, false);

//  LoadStringFromFile (FileName + '\Apache2\conf\httpd.conf', SrcContent4);
//  if Pos('Include "' + FileName2 + '/apache2/conf/alias/usvn.conf"', SrcContent4) = 0 then
//  begin
//    SaveStringToFile(FileName + '\Apache2\conf\httpd.conf', #13#10 + 'Include "' + FileName2 + '/apache2/conf/alias/usvn.conf"' + #13#10, true);
//  end;
  
end;

procedure ConfigPHPini(FileName: String);
var SrcContent4: String;
var FileName2: String;

begin
  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  StringChange (FileName2, '\','/');
  LoadStringFromFile (FileName, SrcContent4);
  StringChangeEx(SrcContent4, ';extension=php_pdo_sqlite.dll', 'extension=php_pdo_sqlite.dll', True);
  DeleteFile (FileName);
  SaveStringToFile(FileName, SrcContent4, false);


end;

procedure ConfigConfig(FileName: String);
var SrcContent4: String;
var FileName2: String;

begin
  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  StringChange (FileName2, '\','/');
  LoadStringFromFile (FileName + '\USVN\config.ini', SrcContent4);
  StringChangeEx(SrcContent4, 'url.base = "/usvn"', 'url.base = "' + URL.Values[0] + '"', True);
  StringChangeEx(SrcContent4, 'subversion.path = ""', 'subversion.path = "' + FileName2 + '/USVN/files/"', True);
  StringChangeEx(SrcContent4, 'subversion.passwd = ""', 'subversion.passwd = "' + FileName2 + '/USVN/files/htpasswd"', True);
  StringChangeEx(SrcContent4, 'subversion.authz = ""', 'subversion.authz = "' + FileName2 + '/USVN/files/authz"', True);
  StringChangeEx(SrcContent4, 'database.options.dbname = ""', 'database.options.dbname = "' + FileName2 + '/USVN/files/usvn.db"', True);
  DeleteFile (FileName + '\USVN\config.ini');
  SaveStringToFile(FileName + '\USVN\config.ini', SrcContent4, false);
  
  //PHP ini
  ConfigPHPini(FileName + '\bin\php\php5.2.5\php.ini');
  ConfigPHPini(FileName + '\\bin\apache\apache2.2.6\bin\php.ini');
end;


procedure ConfigHtAcess(FileName: String);
var SrcContent4: String;
var FileName2: String;

begin
  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  StringChange (FileName2, '\','/');
  LoadStringFromFile (FileName + '\USVN\.htaccess', SrcContent4);
  StringChangeEx(SrcContent4, 'RewriteBase /usvn/', 'RewriteBase ' + URL.Values[0], True);
  DeleteFile (FileName + '\USVN\.htaccess');
  SaveStringToFile(FileName + '\USVN\.htaccess', SrcContent4, false);
end;

procedure ConfigDB(FileName: String);
var FileName2: String;

begin
  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  StringChange (FileName2, '\','/');
  CreateDir(FileName2 + '/USVN/files/svn/');
end;


function URI_NextButtonClick(Sender: TWizardPage): Boolean;
var
    Page: TInputQueryWizardPage;
    tmp1: String;
    tmp2: String;
    i: Integer;

begin
    Result := True;
    Page := TInputQueryWizardPage(Sender);
    URL.Values[0] := AnsiLowercase(URL.Values[0]);
    tmp1 := Copy(URL.Values[0], 0, 1);
    tmp2 := Copy(URL.Values[0], Length(URL.Values[0]), 1);
    i:= CompareStr(tmp1, '/');
    if i <> 0 then begin
      Result := False;
    end;
    i:= CompareStr(tmp2, '/');
    if i <> 0 then begin
      Result := False;
    end;
    if Result = False then begin
      MsgBox('URI incorrect (exemple : /usvn/)', mbCriticalError, MB_OK);
    end;
    
    i:=Length(URL.Values[1]);
    if i = 0 then begin
      Result := False;
      MsgBox('Your login must have at least 1 caractere.', mbCriticalError, MB_OK);
    end;
    i:=Length(URL.Values[2]);
    if i < 8 then begin
      Result := False;
      MsgBox('Your password must have at least 8 caracteres.', mbCriticalError, MB_OK);
    end;
end;

procedure InitializeWizard;
begin
  { Create the pages }

  URL := CreateInputQueryPage(wpWelcome,
    'Personal Information', 'Default USVN URL?',
    'Please specify the USVN URL (ex: /usvn/)');
  URL.OnNextButtonClick := @URI_NextButtonClick;
  URL.Add('URL:', False);
  URL.Add('Login:', False);
  URL.Add('Password:', True);
  URL.Values[0] := '/usvn/';
  URL.Values[1] := 'admin';

  URLstring := URL.Values[0];
end;

function IsAUpdate(FileName: String): Boolean;

begin
  FileName:= ExpandConstant(FileName);
  Result := FileExists(FileName + '\USVN\version.ini');

end;

function GetUSVNVersion(FileName: String): String;
var SrcContent4: String;

begin
  FileName:= ExpandConstant(FileName);
  LoadStringFromFile (FileName + '\USVN\version.ini', SrcContent4);
  Result := SrcContent4;
end;

procedure InfoBox(FileName: String);
var FileName2: String;
var ErrorCode: Integer;
var SrcContent4: String;

begin

  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  StringChange (FileName2, '\','/');
  LoadStringFromFile (FileName + '\USVN\info.txt', SrcContent4);
  StringChangeEx(SrcContent4, 'USVN url : http://localhost/usvn/', 'USVN url : http://localhost' + URL.Values[0], True);
  StringChangeEx(SrcContent4, 'login :', 'login : ' + URL.Values[1], True);
  DeleteFile (FileName + '\USVN\info.txt');
  SaveStringToFile(FileName + '\USVN\info.txt', SrcContent4, false);
  ShellExec('open', FileName2 + '/USVN/info.txt', '', '', SW_SHOW, ewNoWait, ErrorCode)

end;

procedure ConfigInstallBAT(FileName: String);
var FileName2: String;
var ErrorCode: Integer;
var SrcContent4: String;
var Version: String;
var Res: Boolean;

begin
  FileName:= ExpandConstant(FileName);
  FileName2:= FileName;
  StringChange (FileName2, '\','/');

  LoadStringFromFile (FileName + '\USVN\install.bat', SrcContent4);

  Res := IsAUpdate(FileName);
  if Res = True then begin
    Version := GetUSVNVersion(FileName);
    if Version = '0.7.0' then begin
      exit;
    end;
  end;
  if Res = True then begin
  //Upgrade
  StringChangeEx(SrcContent4, 'cd', 'cd ' + FileName + '\USVN\update\' + Version + '\', True);
  StringChangeEx(SrcContent4, 'php', 'php index.php', True);
  end
  else begin
   //First Installation
    StringChangeEx(SrcContent4, 'cd', 'cd ' + FileName + '\USVN\', True);
    StringChangeEx(SrcContent4, 'php', FileName + '\bin\php\php5.2.5\php install/install-commandline.php config.ini .htaccess ' + URL.Values[1] + ' ' + URL.Values[2], True);
  end;
  DeleteFile (FileName + '\USVN\install.bat');
  SaveStringToFile(FileName + '\USVN\install.bat', SrcContent4, false);
  ShellExec('open', FileName2 + '/USVN/install.bat', '', '', SW_HIDE, ewWaitUntilTerminated, ErrorCode)
end;
[Run]
;Filename: "{app}\USVN\svn-1.4.3-setup.exe"; Description: "Launch SVN installation now"; Flags:shellexec postinstall skipifsilent runhidden
