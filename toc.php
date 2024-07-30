<?php

// Function to generate the Table of Contents
function generate_table_of_contents($content) {
    // Initialize the TOC
    $toc = '<div class="table-of-contents normal_rate_card"><h3>Table of Contents</h3><div class="toc-content"><ul>';

    // Match all headings (h1 to h6)
    preg_match_all('/<h([1-6])>(.*?)<\/h[1-6]>/i', $content, $matches, PREG_SET_ORDER);

    // Loop through the matches and create the TOC
    foreach ($matches as $match) {
        $level = $match[1];
        $heading_text = trim($match[2]); // Trim any whitespace
        $anchor_id = sanitize_title($heading_text); // Create a sanitized anchor ID

        // Skip headings with text "Pros" or "Cons"
        if (strcasecmp($heading_text, 'Pros') === 0 || strcasecmp($heading_text, 'Cons') === 0) {
            continue; // Skip this heading
        }

        // Add the anchor ID to the heading in the content
        $content = str_replace($match[0], '<h' . $level . ' id="' . $anchor_id . '">' . $heading_text . '</h' . $level . '>', $content);

        // Indent for subheadings
        $indent = str_repeat('&nbsp;', ($level - 1) * 4);
        $toc .= '<li>' . $indent . '<a href="#' . esc_attr($anchor_id) . '">' . esc_html($heading_text) . '</a></li>';
    }

    $toc .= '</ul></div>';
    $toc .= '<a href="#" class="toc-toggle">Show Less</a></div>';

    // Return the TOC and modified content
    return $toc . $content;
}

// Function to add the TOC to single posts only
function add_toc_to_single_post($content) {
    // Only add TOC if it's a single post
    if (is_single()) {
        // Only add TOC if there are headings
        if (preg_match('/<h[1-6].*?>.*?<\/h[1-6]>/i', $content)) {
            $content = generate_table_of_contents($content);
        }
    }
    return $content;
}

// Hook into the_content filter to add the TOC
add_filter('the_content', 'add_toc_to_single_post');

// Function to add custom code in the header for single posts
function custom_header_code_for_single_posts() {
    if (is_single()) {
        echo '<style>
                .table-of-contents {
                    padding: 20px;
                    margin-bottom: 20px;
                }
                .table-of-contents h3 {
                    margin-top: 0;
                    font-size: 1em;
                    color: #333;
                }
                .toc-content {
                    max-height: none;
                    overflow: hidden;
                    transition: max-height 0.3s ease-in-out;
                }
                .toc-content.collapsed {
                    max-height: 0;
                }
                .table-of-contents ul {
                    list-style-type: none;
                    padding-left: 0;
                    margin: 0;
                }
                .table-of-contents ul li {
                    margin: 5px 0;
                }
                .table-of-contents ul li a {
                    text-decoration: none;
                    color: #0073aa;
                    transition: color 0.3s;
                }
                .table-of-contents ul li a:hover {
                    color: #005177;
                    font-weight: bold;
                }
                .table-of-contents ul li a:focus {
                    outline: 2px dashed #0073aa;
                }
                .toc-toggle {
                    display: block;
                    margin-top: 10px;
                    color: #0073aa;
                    cursor: pointer;
                }
                @media(max-width: 768px) {
                    .table-of-contents {
                        margin: 10px;
                    }
                }
              </style>';

        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var tocToggle = document.querySelector(".toc-toggle");
                    var tocContent = document.querySelector(".toc-content");

                    tocToggle.addEventListener("click", function(event) {
                        event.preventDefault();
                        if (tocContent.classList.contains("collapsed")) {
                            tocContent.classList.remove("collapsed");
                            tocToggle.textContent = "Show Less";
                        } else {
                            tocContent.classList.add("collapsed");
                            tocToggle.textContent = "Show More";
                        }
                    });
                });
              </script>';
    }
}

// Hook into wp_head to run the function
add_action('wp_head', 'custom_header_code_for_single_posts');