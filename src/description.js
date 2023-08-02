import React from 'react';
import ReactDOM from 'react-dom/client';
import DescriptionGenerator from '@newfold-labs/wp-module-ai';

  const handleSuggestionClick = (suggestion) => {
    if(suggestion){
      const inputElement = document.getElementById('blogdescription');
      if(inputElement){
        inputElement.value = suggestion;
      }
      let excerptElement = document.querySelector('#editor .editor-post-excerpt .editor-post-excerpt__textarea textarea');
      if(excerptElement){
        excerptElement.value = suggestion;
      }
    }
  };

  const siteDesc = document.getElementById('blogdescription') && document.getElementById('blogdescription').value;
  const siteTitle = document.getElementById('blogname') && document.getElementById('blogname').value;
  const siteUrl = document.getElementById('home') && document.getElementById('home').value;

  const root = ReactDOM.createRoot(document.getElementById('description-generator-container'));
  root.render(<DescriptionGenerator
    siteDesc={siteDesc}
    siteTitle={siteTitle}
    siteSubtype=""
    siteType=""
    siteUrl={siteUrl}
    handleSuggestionClick={handleSuggestionClick}
  />);
