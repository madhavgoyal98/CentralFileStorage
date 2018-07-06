// JavaScript Document

function changeClass(i)
{
    if(i == 'home')
    {
        document.getElementById('upload').className = ''; 
        document.getElementById('account').className = ''; 
        document.getElementById('home').className = 'active';
    }
    else if(i == 'upload')
    {
        document.getElementById('upload').className = 'active'; 
        document.getElementById('account').className = ''; 
        document.getElementById('home').className = '';
    }
    else if(i == 'account')
    {
        document.getElementById('upload').className = ''; 
        document.getElementById('account').className = 'active'; 
        document.getElementById('home').className = '';
    }
}