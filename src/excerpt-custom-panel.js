import { useState, useEffect } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import DescriptionGenerator from '@newfold-labs/wp-module-ai';

const withCustomPlacement = (WrappedComponent) => {
  return (props) => {
    const [targetElement, setTargetElement] = useState(null);
    const isSidebarOpened = useSelect((select) => select('core/edit-post').isEditorSidebarOpened(), []);

    useEffect(() => {
      const checkForElement = () => {
        const element = document.querySelector('.editor-post-excerpt');
        if (element && !targetElement) {
          setTargetElement(element);
        }
      };

      checkForElement();
      const intervalId = setInterval(checkForElement, 300);
      
      return () => {
        clearInterval(intervalId);
      };
    }, [targetElement]); // Removed isSidebarOpened from the dependency array

    useEffect(() => {
      if (targetElement && isSidebarOpened) {
        const customPanel = document.querySelector('.excerpt-custom-panel');
        if (customPanel && targetElement.parentNode) {
          targetElement.parentNode.insertBefore(customPanel, targetElement.nextSibling);
        }

        return () => {
          if (customPanel && customPanel.parentNode && customPanel.parentNode.contains(customPanel)) {
            customPanel.parentNode.removeChild(customPanel);
          }
        };
      }
    }, [targetElement, isSidebarOpened]);

    return <WrappedComponent {...props} />;
  };
};

function ExcerptCustomPanel() {
  
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

  return (
    <PluginDocumentSettingPanel
      name="excerpt-custom-panel"
      title="Show Excerpt Suggestions"
      className="excerpt-custom-panel"
    >
      <DescriptionGenerator
      siteDesc={ "This is a driving school" }
      siteTitle={ " Driving school site"}
      siteSubtype=""
      siteType=""
      siteUrl={ "google.com"}
      handleSuggestionClick={handleSuggestionClick}
    />
    </PluginDocumentSettingPanel>
  );

}

const EnhancedExcerptCustomPanel = withCustomPlacement(ExcerptCustomPanel);

registerPlugin('excerpt-custom-panel', {
  render: EnhancedExcerptCustomPanel,
});

export default EnhancedExcerptCustomPanel;