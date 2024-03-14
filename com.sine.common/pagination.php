
<?php
function paginate($page, $tpages, $adjacents, $function) {
    $prevlabel = "&lsaquo; Anterior";
    $nextlabel = "Siguiente &rsaquo;";
    $out = '<ul class="col-12 mb-0 mt-2 mx-auto pagination pagination-sm justify-content-end z-1">';

    // previous label
    if ($page == 1) {
        $out .= "<li class='page-item z-1 disabled'><a class='page-link py-0 text-secondary fw-semibold'>$prevlabel</a></li>";
    } else if ($page == 2) {
        $out .= "<li class='page-item z-1'><a href='javascript:void(0);' onclick='".$function."(1)' class='page-link py-0 text-secondary fw-semibold'>$prevlabel</a></li>";
    } else {
        $out .= "<li class='page-item z-1'><a href='javascript:void(0);' onclick='".$function."(".($page-1).")' class='page-link py-0 text-secondary fw-semibold'>$prevlabel</a></li>";
    }
    
    // first label
    if ($page > ($adjacents+1)) {
        $out .= "<li class='page-item z-1'><a href='javascript:void(0);' onclick='".$function."(1)' class='page-link py-0 text-secondary fw-semibold'>1</a></li>";
    }

    // interval
    if ($page > ($adjacents+2)) {
        $out .= "<li class='page-item z-1 disabled'><a class='page-link py-0 text-secondary fw-semibold'>...</a></li>";
    }

    // pages
    $pmin = max($page - $adjacents, 1);
    $pmax = min($page + $adjacents, $tpages);

    for ($i = $pmin; $i <= $pmax; $i++) {
        if ($i == $page) {
            $out .= "<li class='active page-item z-1'><a class='page-link py-0 fw-semibold'>$i</a></li>";
        } else if ($i == 1) {
            $out .= "<li class='page-item z-1'><a href='javascript:void(0);' onclick='".$function."(1)' class='page-link py-0 text-secondary fw-semibold'>$i</a></li>";
        } else {
            $out .= "<li class='page-item z-1'><a href='javascript:void(0);' onclick='".$function."(".$i.")' class='page-link py-0 text-secondary fw-semibold'>$i</a></li>";
        }
    }

    // interval
    if ($page < ($tpages - $adjacents-1)) {
        $out .= "<li class='page-item z-1 disabled'><a class='page-link py-0 text-secondary fw-semibold'>...</a></li>";
    }

    // last
    if ($page < ($tpages - $adjacents)) {
        $out .= "<li class='page-item z-1'><a href='javascript:void(0);' onclick='".$function."($tpages)' class='page-link py-0 text-secondary fw-semibold'>$tpages</a></li>";
    }

    // next
    if ($page < $tpages) {
        $out .= "<li class='page-item z-1'><a href='javascript:void(0);' onclick='".$function."(".($page+1).")' class='page-link py-0 text-secondary fw-semibold'>$nextlabel</a></li>";
    } else {
        $out .= "<li class='page-item z-1 disabled'><a class='page-link py-0 text-secondary fw-semibold'>$nextlabel</a></li>";
    }

    $out .= "</ul>";
    return $out;
}
