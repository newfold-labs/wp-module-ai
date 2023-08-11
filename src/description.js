const { render } = wp.element;
import DescriptionGenerator from '@newfold-labs/wp-module-ai';
import './excerpt-custom-panel';

const siteDesc = document.getElementById('blogdescription') && document.getElementById('blogdescription').value;
const siteTitle = document.getElementById('blogname') && document.getElementById('blogname').value;
const siteUrl = document.getElementById('home') && document.getElementById('home').value;

render(
  <DescriptionGenerator
    siteDesc={siteDesc || "This is a driving school" }
    siteTitle={siteTitle || " Driving school site"}
    siteSubtype=""
    siteType=""
    siteUrl={siteUrl}
    targetElementSelector="#blogdescription"
  />,
  document.getElementById('description-generator-container')
);
