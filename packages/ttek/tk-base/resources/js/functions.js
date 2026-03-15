/**
 * Var dump function for debugging
 */
window.vd = function() {
  if (tkConfig.isProd) return;
  for (let k in arguments) console.log(arguments[k]);
}

/**
 * @param text
 */
window.copyToClipboard = function(text) {
  if (navigator.clipboard) {
    // Modern versions of Chromium browsers, Firefox, etc.
    navigator.clipboard.writeText(text);
  } else if (window.clipboardData) {
    // Internet Explorer.
    window.clipboardData.setData('Text', text);
  } else {
    // Fallback method using Textarea.
    var textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.top = '-999999px';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
      if (!document.execCommand('copy')) {
        console.warn('Could not copy text to clipboard');
      }
    } catch (error) {
      console.warn('Could not copy text to clipboard');
    }
    document.body.removeChild(textArea);
  }
}

