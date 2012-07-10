<link href="../modules/BanckleMeeting/css/style.css" rel="stylesheet" type="text/css" />
<div class="blc-main-container">

{if $data.panel == "activate" OR $data.panel == "settings"}
<div id="panel_activate">
<form method="post" action="">
 <input type="hidden" name="panel" value="activate">
 <input type="hidden" name="active" value="1">
<div class="blc-signin-container">	
    <div class="blc-signin-top">
    	<h1><img src="../modules/BanckleMeeting/images/blc_signin.png" /> Banckle Meeting - Update Widget Code</h1>
        <div class="blc-signin-top-right"><a href="{$data.dashboard_url}">Dashboard</a></div>
    </div>        
    <div class="blc-signin-content">    
    {if $data.login_error != ""}
        <p style="color:red;">{$data.login_error}</p>
        <div class="seprator" style="margin-left:-10px;">&nbsp;</div>
    {/if}        
    <h2>Widget Code</h2>
    <textarea name="widget_code" id="widget_code" style="width:465px; height:100px;">{$data.widget_code}</textarea>    
         
    </div>    	       
    <div class="blc-signin-bottom-left"><button type="submit" class="btn-signin">Update</button> {if $data.widget_code != ""}&nbsp;  <button name="deactivate" type="submit" class="btn-signin">Deactivate</button>{/if}</div>

    <div class="seprator">&nbsp;</div>
    
    <div class="blc-signin-signup-area">Don't have a Banckle account? <a href="http://banckle.com/action/signup" target="_blank">Sign Up</a> now</div>
   
    <div class="clear1">&nbsp;</div>
    
</div>
<div class="clear1">&nbsp;</div>
</form>
</div>	
{else}
<div id="panel_default">
{if $data.active == 1}
	<p>Banckle Meeting is successfully enabled. <a href="{$data.current_url}&panel=settings"><strong>Settings</strong></a></p>
{else}
<p>Banckle Meeting is disabled. Please go to <a href="{$data.current_url}&panel=activate"><strong>page</strong></a> to enable it.</p>
{/if}
<iframe src="https://apps.banckle.com/meeting/" width="100%" height="800" frameborder="0" scrolling="no"></iframe>
</div>
{/if}
</div> <!-- Main Container Ends Here !-->